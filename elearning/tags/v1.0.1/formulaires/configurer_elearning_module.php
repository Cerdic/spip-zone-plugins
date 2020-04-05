<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_elearning_module_charger_dist($id_rubrique) {
	include_spip('inc/config');
	
	$contexte = array(
		'_meta_casier' => 'elearning',
		'_id_rubrique' => intval($id_rubrique),
		'modules' => lire_config('elearning/modules', array()),
	);
	
	return $contexte;
}

function formulaires_configurer_elearning_module_verifier_dist($id_rubrique) {
	include_spip('inc/config');
	
	$id_rubrique = intval($id_rubrique);
	$modules_tout = lire_config('elearning/modules', array());
	$modules = _request('modules');
	
	$modules_tout[$id_rubrique]['jeu'] = $modules[$id_rubrique]['jeu'];
	$modules_tout[$id_rubrique]['score'] = $modules[$id_rubrique]['score'];
	
	set_request('modules', $modules_tout);
	
	return array();
}
