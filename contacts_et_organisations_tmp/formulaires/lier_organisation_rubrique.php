<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_lier_organisation_rubrique_charger_dist($id_rubrique, $redirect=''){
	$valeurs = array(
		'recherche_organisation' => '',
		'id_rubrique' => intval($id_rubrique),
		'redirect' => $redirect
	);
	return $valeurs;
}

function formulaires_lier_organisation_rubrique_verifier_dist($id_rubrique, $redirect=''){
	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts
	return $erreurs;
}

function formulaires_lier_organisation_rubrique_traiter_dist($id_rubrique, $redirect=''){
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
