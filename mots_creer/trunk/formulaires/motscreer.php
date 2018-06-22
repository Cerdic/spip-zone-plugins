<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_motscreer_charger_dist() {
	return array(
		'id_groupe' => '',
		'mots'       => '',
	);
}

function formulaires_motscreer_verifier_dist() {
	$retour = array();
	if (!_request('mots')) {
		$retour['mots'] = _T('info_obligatoire');
	}
	if (!_request('id_groupe')) {
		$retour['id_groupe'] = _T('info_obligatoire');
	}
	if(!autoriser('modifier','groupemots',_request('id_groupe'))){
		$retour['id_groupe'] = _T('motscreer:pas_autorise');
	}
	return $retour;
}

function formulaires_motscreer_traiter_dist() {
	include_spip('action/editer_mot');
	
	$id_groupe = intval(_request('id_groupe'));
	$mots = array_filter(preg_split('#[\r\n]#',_request('mots')));

	foreach ($mots as $mot) {
		if(version_compare($GLOBALS['spip_version_branche'],'3.1','<')){
			$id_mot = mot_inserer($id_groupe);
			mot_modifier($id_mot,array('titre' => $mot));
		} else {
			mot_inserer($id_groupe, array('titre' => $mot));
		}
	}
	
	return array(
		'message_ok' => _T('motscreer:mots_crees'),
		'editable'   => true,
	);
}
