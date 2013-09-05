<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

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
	$cadence = array(60,5);
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
	$current = ((isset($GLOBALS['meta']['mailshot_processing']) AND $GLOBALS['meta']['mailshot_processing'])?true:false);

	$new = false;
	if ($force OR sql_countsel("spip_mailshots","statut=".sql_quote('processing')))
		$new = true;

	if ($new OR $new!==$current){
		if ($new) {
			ecrire_meta("mailshot_processing",'oui');
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
 * @return int
 *   nombre de mails envoyes
 */
function mailshot_envoyer_lot($nb_max=5){
	$nb_restant = $nb_max;
	$now = $_SERVER['REQUEST_TIME'];
	if (!$now) $now=time();
	define('_MAILSHOT_MAX_TIME',$now+15); // 15s maxi
	define('_MAILSHOT_MAX_TRY',5); // 5 essais maxis par destinataires

	// on traite au maximum 2 serie d'envois dans un appel
	$shot = sql_allfetsel("*","spip_mailshots","statut=".sql_quote('processing'),'','id_mailshot','0,2');
	foreach($shot as $shoot){
		spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']." ".$shoot['current']."/".$shoot['total']." (max $nb_max)","mailshot");

		// verifier que la liste des destinataires est OK
		mailshot_initialiser_destinataires($shoot);
		if (time()>_MAILSHOT_MAX_TIME) return $nb_restant;

		// chercher les N prochains destinataires
		$dests = sql_allfetsel("*","spip_mailshots_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('todo'),'','try',"0,$nb_max");
		if (count($dests)){
			$options = array('tracking_id'=>"mailshot".intval($shoot['id_mailshot']));
			$subscriber = charger_fonction("subscriber","newsletter");
			$send = charger_fonction("send","newsletter");
			$corps = array("sujet"=>&$shoot['sujet'],"html"=>&$shoot['html'],"texte"=>&$shoot['texte']);
			foreach($dests as $d){
				if (time()>_MAILSHOT_MAX_TIME) return $nb_restant;
				$s = $subscriber($d['email']);
				$erreur = $send($s, $corps, $options);
				$try = $d['try']+1;
				if ($erreur){
					if ($try>=_MAILSHOT_MAX_TRY
						OR preg_match(",@example\.org$,i",$s['email'])
					  OR defined('_TEST_EMAIL_DEST')){
						sql_updateq("spip_mailshots_destinataires",array('statut'=>'fail','try'=>$try,'date'=>date('Y-m-d H:i:s')),"id_mailshot=".intval($shoot['id_mailshot'])." AND email=".sql_quote($d['email']));
						sql_update("spip_mailshots",array("current"=>"current+1","failed"=>"failed+1"),"id_mailshot=".intval($shoot['id_mailshot']));
						spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']."/".$d['email']." : Erreur [$erreur] / failed apres $try essais","mailshot"._LOG_ERREUR);
					}
					else {
						sql_updateq("spip_mailshots_destinataires",array('try'=>$try,'date'=>date('Y-m-d H:i:s')),"id_mailshot=".intval($shoot['id_mailshot'])." AND email=".sql_quote($d['email']));
						spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']."/".$d['email']." : Probleme [$erreur] (essai $try)","mailshot"._LOG_INFO_IMPORTANTE);
					}
				}
				else {
					$nb_restant--;
					sql_updateq("spip_mailshots_destinataires",array('statut'=>'sent','try'=>$try,'date'=>date('Y-m-d H:i:s')),"id_mailshot=".intval($shoot['id_mailshot'])." AND email=".sql_quote($d['email']));
					sql_update("spip_mailshots",array("current"=>"current+1"),"id_mailshot=".intval($shoot['id_mailshot']));
					spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']."/".$d['email']." OK","mailshot");
				}
				$nb_max--;
			}
			// si $nb_max non nul verifier qu'il n'y a plus de dests sur cette envoi pour maj le statut juste en dessous
			if ($nb_max)
				$dests = sql_allfetsel("*","spip_mailshots_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('todo'),'','try',"0,$nb_max");
		}

		if (!count($dests)){
			// plus de destinataires ? on a fini, on met a jour compteur et statut
			$sent = sql_countsel("spip_mailshots_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('sent'));
			$failed = sql_countsel("spip_mailshots_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('fail'));
			$set = array(
				'statut' => 'end',
				'failed' => $failed,
				'current' => $sent+$failed,
				'date' => date('Y-m-d H:i:s'),
			);
			sql_updateq("spip_mailshots",$set,"id_mailshot=".intval($shoot['id_mailshot']));
			mailshot_update_meta_processing();
		}
		if (time()>_MAILSHOT_MAX_TIME) return $nb_restant;
	}

	return 0; // plus rien a envoyer sur ce lot
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
		$nb_lot = 100;
		$current = $nbd;
		$listes = explode(',',$shoot['listes']);
		$now = date('Y-m-d H:i:s');
		$subscribers = charger_fonction("subscribers","newsletter");
		do {
			if (time()>_MAILSHOT_MAX_TIME) return;
			$limit = "$current,".($current+$nb_lot);
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