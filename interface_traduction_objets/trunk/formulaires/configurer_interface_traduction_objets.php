<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('prive/formulaires/configurer_multilinguisme');

function formulaires_configurer_interface_traduction_objets_charger_dist() {
	$valeurs = array();

	foreach (array('desactiver_interface_traduction', 'desactiver_liste_compacte') as $m) {
		$valeurs[$m] = explode(',', isset($GLOBALS['meta'][$m]) ? $GLOBALS['meta'][$m] : '');
	}

	return $valeurs;
}


function formulaires_configurer_interface_traduction_objets_traiter_dist() {
	$res = array('editable' => true);
	// un checkbox seul de name X non coche n'est pas poste.
	// on verifie le champ X_check qui indique que la checkbox etait presente dans le formulaire.

	foreach (array('desactiver_interface_traduction', 'desactiver_liste_compacte') as $m) {
		if (!is_null($v = _request($m))) {
			// join et enlever la valeur vide ''
			ecrire_meta($m, implode(',', array_diff($v, array(''))));
		}
	}


	$res['message_ok'] = _T('config_info_enregistree');

	return $res;
}
