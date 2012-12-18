<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


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
	if ($force OR sql_countsel("spip_mailshot","statut=".sql_quote('processing')))
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
	$nb = 0;
	$now = $_SERVER['REQUEST_TIME'];
	if (!$now) $now=time();
	define('_MAILSHOT_MAX_TIME',$now+15); // 15s maxi

	// on traite au maximum 2 serie d'envois dans un appel
	$shot = sql_allfetsel("*","spip_mailshot","statut=".sql_quote('processing'),'','id_mailshot','0,2');
	foreach($shot as $shoot){
		spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']." ".$shoot['current']."/".$shoot['total']." (max $nb_max)","mailshot");

		// verifier que la liste des destinataires est OK
		mailshot_initialiser_destinataires($shoot);

		// chercher les N prochains destinataires
		$dests = sql_allfetsel("*","spip_mailshot_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('todo'),'','',"0,$nb_max");
		if (count($dests)){
			$subscriber = charger_fonction("subscriber","newsletter");
			$send = charger_fonction("send","newsletter");
			$corps = array("sujet"=>&$shoot['sujet'],"html"=>&$shoot['html'],"texte"=>&$shoot['texte']);
			foreach($dests as $d){
				if (time()>_MAILSHOT_MAX_TIME) return $nb;
				$s = $subscriber($d['email']);
				$erreur = $send($s, $corps);
				if ($erreur){
					sql_updateq("spip_mailshot_destinataires",array('statut'=>'fail','date'=>date('Y-m-d H:i:s')),"id_mailshot=".intval($shoot['id_mailshot'])." AND email=".sql_quote($d['email']));
					sql_update("spip_mailshot",array("current"=>"current+1","failed"=>"failed+1"),"id_mailshot=".intval($shoot['id_mailshot']));
					spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']."/".$d['email']." : $erreur","mailshot"._LOG_ERREUR);
				}
				else {
					$nb++;
					sql_updateq("spip_mailshot_destinataires",array('statut'=>'sent','date'=>date('Y-m-d H:i:s')),"id_mailshot=".intval($shoot['id_mailshot'])." AND email=".sql_quote($d['email']));
					sql_update("spip_mailshot",array("current"=>"current+1"),"id_mailshot=".intval($shoot['id_mailshot']));
					spip_log("mailshot_envoyer_lot #".$shoot['id_mailshot']."/".$d['email']." OK","mailshot");
				}
				$nb_max--;
			}
			// si $nb_max non nul verifier qu'il n'y a plus de dests sur cette envoi pour maj le statut juste en dessous
			if ($nb_max)
				$dests = sql_allfetsel("*","spip_mailshot_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('todo'),'','',"0,$nb_max");
		}

		if (!count($dests)){
			// plus de destinataires ? on a fini, on met a jour compteur et statut
			$sent = sql_countsel("spip_mailshot_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('sent'));
			$failed = sql_countsel("spip_mailshot_destinataires","id_mailshot=".intval($shoot['id_mailshot'])." AND statut=".sql_quote('fail'));
			$set = array(
				'statut' => 'end',
				'failed' => $failed,
				'current' => $sent+$failed,
			);
			sql_updateq("spip_mailshot",$set,"id_mailshot=".intval($shoot['id_mailshot']));
			mailshot_update_meta_processing();
		}
		if (time()>_MAILSHOT_MAX_TIME) return $nb;
	}

	return $nb;
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
	$nbd = sql_countsel("spip_mailshot_destinataires","id_mailshot=".intval($shoot['id_mailshot']));
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

				if (!sql_insertq_multi('spip_mailshot_destinataires',$ins)){
					foreach ($ins as $i){
						sql_insertq('spip_mailshot_destinataires',$i);
						if (time()>_MAILSHOT_MAX_TIME) return;
					}
				}
			}
			$current = $current + count($dests);
		}
		while (count($dests));

		// ici on a fini toutes les init des destinataires
		// on remet a jour le compteur de total au cas ou
		$nbd = sql_countsel("spip_mailshot_destinataires","id_mailshot=".intval($shoot['id_mailshot']));
		if ($nbd<$shoot['total'])
			sql_updateq("spip_mailshot",array('total'=>$nbd),"id_mailshot=".intval($shoot['id_mailshot']));
		spip_log("mailshot_initialiser_destinataires #".$shoot['id_mailshot']." OK ($nbd)","mailshot");
	}

}