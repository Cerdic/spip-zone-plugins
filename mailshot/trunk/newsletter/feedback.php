<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/config");

/**
 * Permettre la prise en compte d'un evenement concernant un mail envoye
 * (tracking de l'ouverture, clic, bounce...)
 * pour mettre a jour le status de l'envoi correspondant
 *
 * @param string $quoi
 *   read : l'email a ete ouvert
 *   clic : un lien a ete clique
 *   soft_bounce : refus temporaire pour cause de boite mail pleine ou autre
 *   hard_bounce : adresse foireuse, refus definitif
 *   reject : email rejete
 *   spam : email taggue en spam
 * @param $email
 * @param $tracking_id
 */
function newsletter_feedback_dist($quoi,$email,$tracking_id){

	if (!in_array($quoi,array('read','clic','soft_bounce','hard_bounce','reject','spam'))){
		spip_log("$quoi inconnu ","newsletter_feedback"._LOG_INFO_IMPORTANTE);
		return;
	}

	if (!preg_match(',^mailshot(\d+)$,',$tracking_id,$m)
		OR !intval($id_mailshot=$m[1])){
		spip_log("tracking_id $tracking_id inconnu","newsletter_feedback"._LOG_INFO_IMPORTANTE);
		return;
	}

	if (!$row = sql_fetsel("*","spip_mailshots_destinataires","id_mailshot=".intval($id_mailshot)." AND email=".sql_quote($email))){
		spip_log("email $email introuvable dans lot mailshot #$id_mailshot","newsletter_feedback"._LOG_INFO_IMPORTANTE);
		return;
	}

	$set = array();
	$desabonner = false;
	// $row['statut'] in todo, sent, fail, [read, [clic]],[spam]
	// ok on a tout ce qu'il faut, avisons
	switch($quoi){
		case 'read':
			if (in_array($row['statut'],array('todo','sent','fail','spam')))
				$set['statut'] = 'read';
			break;
		case 'clic':
			if (in_array($row['statut'],array('todo','sent','fail','spam','read')))
				$set['statut'] = 'clic';
			break;
		case 'spam':
			if (in_array($row['statut'],array('todo','sent','fail','read')))
				$set['statut'] = 'spam';
			break;
		case 'reject':
		case 'hard_bounce':
			if (in_array($row['statut'],array('todo','sent','fail'))){
				$set['statut'] = 'fail';
				$desabonner = true;
			}
			break;
		case 'soft_bounce':
			if (in_array($row['statut'],array('todo','sent','fail')))
				$set['statut'] = 'fail';
			break;
	}

	if (count($set)){
		spip_log("lot #$id_mailshot | $quoi $email : passe en statut=".$set['statut'].($desabonner?"( et unsubscribe)":''),"newsletter_feedback"._LOG_INFO_IMPORTANTE);
		sql_updateq("spip_mailshots_destinataires",$set,"id_mailshot=".intval($id_mailshot)." AND email=".sql_quote($email));
		if ($desabonner){
			$unsubscribe = charger_fonction("unsubscribe","newsletter");
			$unsubscribe($email);
		}
	}
	else {
		spip_log("lot #$id_mailshot | $quoi $email ras","newsletter_feedback");
	}

}
