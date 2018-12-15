<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Mettre a jour les stats d'un envoi
 * @param $id_mailshot
 */
function mailshot_compter_envois($id_mailshot){

	// todo, sent, fail, [read, [clic]],[spam]
	$total = sql_countsel("spip_mailshots_destinataires","id_mailshot=".intval($id_mailshot));
	// si aucun destinataires en base on ne fait rien, pour ne pas risquer de tuer les stats d'un envoi purge
	if (!$total) {
		return;
	}

	$statuts = sql_allfetsel("statut,count(email) as nb","spip_mailshots_destinataires","id_mailshot=".intval($id_mailshot),"statut");
	$statuts = array_combine(array_map('reset',$statuts),array_map('end',$statuts));
	#var_dump($statuts);
	$set = array(
		'total' => $total,
		'failed'  => 0,
		"nb_read" => 0,
		"nb_clic" => 0,
		"nb_spam" => 0,
	);
	if (isset($statuts['fail']))
		$set['failed'] = $statuts['fail'];
	if (isset($statuts['read']))
		$set['nb_read'] = $statuts['read'];
	if (isset($statuts['clic'])){
		$set['nb_read'] += $statuts['clic']; // les clics sont aussi des lus
		$set['nb_clic'] = $statuts['clic'];
	}
	if (isset($statuts['spam']))
		$set['nb_spam'] = $statuts['spam'];

	// current c'est tous les envoyes (y compris fails)
	unset($statuts['todo']);
	$set['current'] = array_sum($statuts);
	#var_dump($set);
	sql_updateq("spip_mailshots",$set,"id_mailshot=".intval($id_mailshot));
}


/**
 * Archiver un envoi (vieux en cron ou a la demande)
 * @param $id_mailshot
 */
function mailshot_archiver($id_mailshot){
	// mettre a jour les stats avant de purger
	mailshot_compter_envois($id_mailshot);
	sql_delete("spip_mailshots_destinataires",'id_mailshot='.intval($id_mailshot));
	sql_updateq("spip_mailshots",array('statut'=>'archive'),'id_mailshot='.intval($id_mailshot));
	spip_log("Archiver mailshot $id_mailshot","mailshot");
}

/**
 * Definir la combinaison (periode,nb envois) pour respecter la cadence maxi configuree
 *
 * @return array
 *   (periode du cron, nb envois a chaque appel)
 */
function mailshot_cadence(){
	include_spip('inc/config');
	if (lire_config("mailshot/boost_send")=='oui')
		return array(30,100); // autant que possible, toutes les 30s

	// cadence maxi
	$cadence = array(60,10);
	$max_rate = lire_config("mailshot/rate_limit");
	if ($max_rate = intval($max_rate)){
		$rate_one_per_one = 24*60*60/$cadence[0];
		if ($max_rate<$rate_one_per_one){
			// 1 mail toutes les N secondes pour ne pas en envoyer plus que le rate demande
			$cadence = array(intval(ceil($rate_one_per_one/$max_rate*$cadence[0])),1);
		}
		else if($max_rate>$rate_one_per_one*$cadence[1]){
			// rien on garde la cadence maxi
		}
		else {
			// envoyer N mails toutes les M secondes pour respecter la cadence max
			$nb = $max_rate/$rate_one_per_one;
			// on se cale sur le N superieur et on ajuste le delai entre chaque envoi
			$nb = intval(ceil($nb));
			$cadence = array(intval(ceil($nb*$rate_one_per_one/$max_rate*$cadence[0])),$nb);
		}
	}

	spip_log("cadence:".implode(",",$cadence),"mailshot");
	return $cadence;
}

/**
 * Mettre a jour la meta qui indique qu'au moins un envoi est en cours
 * evite un acces sql a chaque hit du cron
 *
 * @param bool $force
 * @return bool
 */
function mailshot_update_meta_processing($force = false){
	$current = ((isset($GLOBALS['meta']['mailshot_processing']) AND $GLOBALS['meta']['mailshot_processing'])?$GLOBALS['meta']['mailshot_processing']:false);

	$new = false;
	if ($force OR sql_countsel("spip_mailshots","statut=".sql_quote('processing')))
		$new = 'oui';
	if ($new===false and $next = sql_getfetsel('date_start','spip_mailshots',"statut=".sql_quote('init'),'','date_start','0,1')){
		$new = strtotime($next);
		if ($new>$_SERVER['REQUEST_TIME']){
			$new = 'oui';
		}
	}

	if ($new OR $new!==$current){
		if ($new) {
			ecrire_meta("mailshot_processing",$new);
			// reprogrammer le cron
			include_spip('inc/genie');
	    genie_queue_watch_dist();
		}
		else
			effacer_meta('mailshot_processing');
	}

	return $new;
}

/**
 * Envoyer une serie de mails
 * @param int $nb_max
 * @param int $offset
 *   pour ne pas commencer au debut de la liste (utile pour les processus paralleles)
 * @return int
 *   nombre de mails envoyes
 */
function mailshot_envoyer_lot($nb_max=5,$offset=0){
	$nb_restant = $nb_max;
	$now = $_SERVER['REQUEST_TIME'];
	if (!$now) $now=time();
	define('_MAILSHOT_MAX_TIME',$now+25); // 25s maxi
	define('_MAILSHOT_MAX_TRY',5); // 5 essais maxis par destinataires

	$offset = intval($offset);
	// on traite au maximum 2 serie d'envois dans un appel
	$shot = sql_allfetsel("*","spip_mailshots","statut=".sql_quote('processing'),'','id_mailshot','0,2');
	foreach($shot as $shoot){
		spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']." ".$shoot['current']."/".$shoot['total']." (max $nb_max)","mailshot");

		// verifier que la liste des destinataires est OK
		mailshot_initialiser_destinataires($shoot);
		if (time()>_MAILSHOT_MAX_TIME) return $nb_restant;
		$listes = explode(',',$shoot['listes']);

		// chercher les N prochains destinataires
		$dests = sql_allfetsel("*","spip_mailshots_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('todo'),'','try',"$offset,$nb_max");
		if (count($dests)){
			$options = array('tracking_id'=>"mailshot".intval($shoot['id_mailshot'])."-".date('Ym',strtotime($shoot['date_start'])));
			$subscriber = charger_fonction("subscriber","newsletter");
			$send = charger_fonction("send","newsletter");
			$corps = array(
				'sujet' => &$shoot['sujet'],
				'html' => &$shoot['html'],
				'texte' => &$shoot['texte'],
				'from' => $shoot['from_email'],
				'nom_envoyeur' => $shoot['from_name'],
			);
			foreach($dests as $d){
				if (time()>_MAILSHOT_MAX_TIME) return $nb_restant;
				$s = $subscriber($d['email'],array('listes'=>$listes));
				$done = false;
				if (!$done = mailshot_verifier_email_envoi_bloque($d['email'], $s, $shoot)) {
					spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']."/".$d['email']." send","mailshot");
					$erreur = $send($s, $corps, $options);
					$try = $d['try']+1;
					if ($erreur){
						if ($try>=_MAILSHOT_MAX_TRY
							OR preg_match(",@example\.org$,i",$s['email'])
						  OR defined('_TEST_EMAIL_DEST')){
							$done = array(
								'fail' => true,
								'statut' => 'fail',
								'try' => $try,
								'date'=>date('Y-m-d H:i:s'),
								'log' => "ERREUR [$erreur] / failed apres $try essais",
							);
							// si c'est un fail max_try verifier et desinscrire eventuellement
							if ($try>1){
								mailshot_verifier_email_fail($d['email']);
							}
						}
						else {
							$done = array(
								'try' => $try,
								'date'=>date('Y-m-d H:i:s'),
								'log' => "INFO Probleme [$erreur] (essai $try)",
							);
						}
					}
					else {
						$done = array(
							'done' => true,
							'statut'=>'sent',
							'try'=>$try,
							'date'=>date('Y-m-d H:i:s'),
							'log' => "OK",
						);
					}
				}
				if ($done) {
					if (isset($done['done'])) {
						$nb_restant--;
						unset($done['done']);
						sql_update("spip_mailshots",array("current"=>"current+1"),"id_mailshot=".intval($shoot['id_mailshot']));
					}
					if (isset($done['fail'])) {
						unset($done['fail']);
						sql_update("spip_mailshots",array("current"=>"current+1","failed"=>"failed+1"),"id_mailshot=".intval($shoot['id_mailshot']));
					}
					if (isset($done['log'])) {
						$loglevel = '';
						if (strncmp($done['log'], 'ERREUR', 6) == 0) {
							$loglevel = _LOG_ERREUR;
						}
						elseif (strncmp($done['log'], 'INFO', 4) == 0) {
							$loglevel = _LOG_INFO_IMPORTANTE;
						}
						spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']."/".$d['email']." : " . $done['log'], "mailshot" . $loglevel);
						unset($done['log']);
					}
					sql_updateq("spip_mailshots_destinataires", $done,"id_mailshot=".intval($shoot['id_mailshot'])." AND email=".sql_quote($d['email']));
				}
				$nb_max--;
			}
			// si $nb_max non nul verifier qu'il n'y a plus de dests sur cette envoi pour maj le statut juste en dessous
			if ($nb_max)
				$dests = sql_allfetsel("*","spip_mailshots_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('todo'),'','try',"$offset,$nb_max");
		}

		if ($nb_max AND !count($dests) AND $offset==0){
			// plus de destinataires ? on a fini, on met a jour compteur et statut
			$set = array(
				'statut' => 'end',
				'date' => date('Y-m-d H:i:s'),
			);
			sql_updateq("spip_mailshots",$set,"id_mailshot=".intval($shoot['id_mailshot']));
			mailshot_compter_envois($shoot['id_mailshot']);
			mailshot_update_meta_processing();
		}
		if (!$nb_max OR time()>_MAILSHOT_MAX_TIME) return $nb_restant;
	}

	return 0; // plus rien a envoyer sur ce lot
}

/**
 * Verifie avant nouvel envoi un email et bloque un envoi si besoin
 *
 * @param string $email
 * @param array $subscriber
 * @param array $shoot
 * @return array|bool
 */
function mailshot_verifier_email_envoi_bloque($email, $subscriber, $shoot) {
	static $envois_idem = array();
	static $test_email_vu = array();

	if (preg_match(",@example\.org$,i",$subscriber['email'])) {
		return array(
			'fail' => true,
			'statut' => 'fail',
			'date' => date('Y-m-d H:i:s'),
			'log' => "INFO Envoi BLOQUE Email obfusque ".$subscriber['email']."",
		);
	}
	// si _TEST_EMAIL_DEST
	if (defined('_TEST_EMAIL_DEST')) {
		if (!isset($test_email_vu[$shoot['id_mailshot']])) {
			$test_email_vu[$shoot['id_mailshot']] = 0;
		}

		// si _TEST_EMAIL_DEST est un email, on en laisse passer 5 envois pour les tests et puis c'est tout
		if (!_TEST_EMAIL_DEST or $test_email_vu[$shoot['id_mailshot']]++>4) {
			$erreur = _T('mailshot:erreur_envoi_mail_force_debug', array('email' => _TEST_EMAIL_DEST));

			return array(
				'fail' => true,
				'statut' => 'fail',
				'date' => date('Y-m-d H:i:s'),
				'log' => "INFO Envoi BLOQUE $erreur",
			);
		}
	}

	if ($shoot['graceful']) {
		// trouver tous les envois pour le meme id
		if (!isset($envois_idem[$shoot['id_mailshot']])) {
			$idem = sql_allfetsel('id_mailshot', 'spip_mailshots', 'id='.sql_quote($shoot['id'],'','text'));
			$envois_idem[$shoot['id_mailshot']] = array_column($idem, 'id_mailshot');
		}
		if ($last = sql_fetsel('*', 'spip_mailshots_destinataires',
			array(
				'email=' . sql_quote($email),
				sql_in('statut', array('todo', 'fail'), 'NOT'),
				sql_in('id_mailshot', $envois_idem[$shoot['id_mailshot']]),
			),'', 'date DESC', '0,1')) {
			// on a trouve un envoi deja fait a la meme newsletter, on passe notre tour pout celui-ci
			$done = array(
				'done' => true,
				'statut' => $last['statut'],
				'date' => $last['date'],
				'log' => "INFO Envoi BLOQUE newsletter #".$shoot['id']." deja envoyee a $email par mailshot#" . $last['id_mailshot'] . " / " . $last['date']
			);
			return $done;
		}
	}

	return false;

}

/**
 * Verifier un email en fail et si plus de N fails consecutifs le desabonner (email foireux)
 * (MAIS ignorer les envois qui n'ont que des fails car c'est un blocage du compte du prestataire d'envoi)
 * @param $email
 */
function mailshot_verifier_email_fail($email) {
	static $mailshot_valides = array();

	if (_MAILSHOT_DESABONNER_FAILED != false) {
		if (!defined('_MAILSHOT_MAX_FAIL')) {
			define('_MAILSHOT_MAX_FAIL', 3);
		}

		$historique = sql_allfetsel(
			'id_mailshot, date, statut, try',
			'spip_mailshots_destinataires',
			'statut!=' . sql_quote('todo') . ' AND email=' . sql_quote($email),
			'',
			'date DESC',
			"0," . _MAILSHOT_MAX_FAIL
		);

		$nb_failed = 0;
		foreach ($historique as $h) {
			if ($h['statut'] == 'fail' AND $h['try'] > 1) {
				// on ne compte le fail que si l'envoi a reussi au moins une fois
				// pour ne pas perdre tous les abonnes quand le service d'envoi bloque le compte
				if (!isset($mailshot_valides[$h['id_mailshot']])) {
					$mailshot_valides[$h['id_mailshot']] = sql_getfetsel('id_mailshot', 'spip_mailshots_destinataires', sql_in('statut', array('todo','fail'),'NOT').' AND id_mailshot='.intval($h['id_mailshot']),'','','0,1');
				}
				if ($mailshot_valides[$h['id_mailshot']]) {
					$nb_failed++;
				}
			}
		}
		if ($nb_failed >= _MAILSHOT_MAX_FAIL) {
			$unsubscribe = charger_fonction("unsubscribe", "newsletter");
			$unsubscribe($email, array('notify' => false));
		}
	}
}

/**
 * Initialiser les destinataires d'un envoi
 * = noter tous les emails a qui envoyer, au debut
 * (fige la liste en debut d'envoi, evite les risques de sauter un destinataire si on se base seulement sur un compteur
 * et sur les abonnes en cours car certains peuvent se desabonner pendant le processus d'envoi qui dure dans le temps)
 *
 * @param array $shoot
 */
function mailshot_initialiser_destinataires($shoot){

	// verifier qu'on a bien initialise tous les destinataires
	$nbd = sql_countsel("spip_mailshots_destinataires","id_mailshot=".intval($shoot['id_mailshot']));
	if ($nbd<$shoot['total']){
		spip_log("mailshot_initialiser_destinataires #".$shoot['id_mailshot']." : $nbd/".$shoot['total'],"mailshot");

		// sinon reprendre l'insertion
		$nb_lot = 2500;
		$current = $nbd;
		$listes = explode(',',$shoot['listes']);
		$now = date('Y-m-d H:i:s');
		$subscribers = charger_fonction("subscribers","newsletter");
		do {
			if (time()>_MAILSHOT_MAX_TIME) return;
			$limit = "$current,$nb_lot";
			$dests = $subscribers($listes,array('limit'=>$limit));

			if (count($dests)){
				// preparer les insertions
				$ins = array();
				foreach ($dests as $d){
					$ins[] = array(
						'id_mailshot' => $shoot['id_mailshot'],
						'email' => $d['email'],
						'date' => $now,
						'statut' => 'todo',
					);
				}

				if (!sql_insertq_multi('spip_mailshots_destinataires',$ins)){
					foreach ($ins as $i){
						sql_insertq('spip_mailshots_destinataires',$i);
						if (time()>_MAILSHOT_MAX_TIME) return;
					}
				}
			}
			$current = $current + count($dests);
		}
		while (count($dests));

		// ici on a fini toutes les init des destinataires
		// on remet a jour le compteur de total au cas ou
		$nbd = sql_countsel("spip_mailshots_destinataires","id_mailshot=".intval($shoot['id_mailshot']));
		if ($nbd<$shoot['total'])
			sql_updateq("spip_mailshots",array('total'=>$nbd),"id_mailshot=".intval($shoot['id_mailshot']));
		spip_log("mailshot_initialiser_destinataires #".$shoot['id_mailshot']." OK ($nbd)","mailshot");
	}

}