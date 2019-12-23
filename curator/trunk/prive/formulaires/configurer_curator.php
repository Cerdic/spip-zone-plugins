<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/chercher_rubrique');

// function formulaires_configurer_curator_charger_dist(){
// }

// function formulaires_configurer_curator_verifier_dist(){
// }

function formulaires_configurer_curator_traiter_dist(){
	
	include_spip('inc/meta');

	$config = array();
	$liste = explode(" ","id_parent statut_souhaite groupe_mots");

	// Réinitailiser
	if (_request('reinit')) {
		foreach ($liste as $v) {
			set_request($v);
		}
		effacer_config('curator');
	}
	// Sauvegarder
	else {
		foreach ($liste as $v) {
				$config[$v] = _request($v);
		}	
		ecrire_config('curator', $config);
	}

	return array('message_ok' => _T('config_info_enregistree') );
}
