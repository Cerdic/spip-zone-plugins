<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_elearning_traiter_dist() {
	include_spip('inc/cvt_configurer');
	$retours = array();
	
	// Avec cette nouvelle rubrique, on reconfigure tout le système
	elearning_mettre_a_jour_les_zones(_request('rubrique_elearning'));
	
	// On enregistre la nouvelle valeur dans la config
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_elearning', array());
	
	$retours['message_ok'] = _T('config_info_enregistree') . $trace;
	$retours['editable'] = true;
	
	return $retours;
}
