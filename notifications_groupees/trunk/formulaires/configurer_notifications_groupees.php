<?php
/*
 * Plugin Notifications groupees
 * (c) 2013
 * Distribue sous licence GPL
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_notifications_groupees_charger_dist(){
	foreach(array(
		"notifications_groupees_qui",
		"notifications_groupees_periode",
		"initialiser",
		"notifications_groupees_evenements",
		) as $m)
		$valeurs[$m] = $GLOBALS['meta'][$m];

	return $valeurs;
}

function formulaires_configurer_notifications_groupees_verifier_dist(){
	$erreurs = array();
	$quand = _request('notifications_groupees_periode');
	if (empty($quand)) $erreurs['message_erreur'] = _T('info_obligatoire');
	return $erreurs;
}

function formulaires_configurer_notifications_groupees_traiter_dist(){
	$res = array('editable'=>true);
	$res['message_ok'] = _T('config_info_enregistree');
	$evts = serialize(_request('notifications_groupees_evenements'));
	_request('notifications_groupees_evenements') ? ecrire_meta(notifications_groupees_evenements,$evts) : effacer_meta(notifications_groupees_evenements);
	if (_request('notifications_groupees_qui') == '1') {
		sql_updateq("spip_forum",array('notifications_groupees'=>1));
		sql_updateq("spip_auteurs",array('notifications_groupees'=>1));
	}
	if (_request('notifications_groupees_qui') == '0') {
		sql_updateq("spip_forum",array('notifications_groupees'=>0));
		sql_updateq("spip_auteurs",array('notifications_groupees'=>0));
	}
	if (_request('initialiser')=='1') {
		effacer_meta('notifications_groupees_derniere');
		$id_job = job_queue_add("notifications_groupees","envoi notifs groupees",array(0),"genie/");
		include_spip('inc/queue');
		queue_schedule(array($id_job));
		$res['message_ok'] .= "<br />"._T("notifications_groupees:initialisation_ok");
	}
	ecrire_meta(notifications_groupees_periode, _request('notifications_groupees_periode'));
	ecrire_meta(notifications_groupees_qui, _request('notifications_groupees_qui'));
	return $res;
}