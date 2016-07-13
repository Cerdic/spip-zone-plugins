<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/mailsubscribers');
include_spip('inc/editer');

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_email_subscription_charger_dist($email){

	if (!$email) return false;
	$listes_dispos = mailsubscribers_listes();
	if (!$listes_dispos) return false;

	$valeurs = array(
		'listes' => array(),
		'_listes_dispo' => $listes_dispos,
		'_email' => $email,
		'_id_mailsubscriber'=>'',
	);
	$subscriber = charger_fonction('subscriber','newsletter');
	$infos = $subscriber($email);
	if ($infos and isset($infos['listes'])){
		$valeurs['listes'] = array_map('mailsubscribers_normaliser_nom_liste',$infos['listes']);
		$valeurs['_id_mailsubscriber'] = sql_getfetsel('id_mailsubscriber','spip_mailsubscribers','email='.sql_quote($email)." OR email=".sql_quote(mailsubscribers_obfusquer_email($email)));
	}
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_email_subscription_verifier_dist($email){
	$erreurs = array();
	$listes = _request('listes');
	if (!$listes)
		$listes = array();

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_email_subscription_traiter_dist($email){

	$listes = _request('listes');
	if (!$listes)
		$listes = array();

	$deja = array();
	$subscriber = charger_fonction('subscriber','newsletter');
	$infos = $subscriber($email);
	if ($infos and isset($infos['listes'])){
		$deja = $infos['listes'];
	}

	$remove = array_diff($deja, $listes);
	$add = array_diff($listes, $deja);

	if (count($remove)){
		$unsubscribe = charger_fonction('unsubscribe','newsletter');
		$unsubscribe($email,array('listes'=>$remove,'notify'=>false));
	}
	if (count($add)){
		$subscribe = charger_fonction('subscribe','newsletter');
		$subscribe($email,array('listes'=>$add,'notify'=>false));
	}

	set_request('listes');
	$res = array('editable' => true,'message_ok'=>'');
	return $res;
}
