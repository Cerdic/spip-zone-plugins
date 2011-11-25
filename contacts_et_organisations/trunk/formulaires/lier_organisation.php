<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_lier_organisation_charger_dist($id_contact, $redirect=''){
	$valeurs = array(
		'recherche_organisation' => '',
		'id_contact' => intval($id_contact),
		'redirect' => $redirect
	);
	return $valeurs;
}

function formulaires_lier_organisation_verifier_dist($id_contact, $redirect=''){
	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts
	return $erreurs;
}

function formulaires_lier_organisation_traiter_dist($id_contact, $redirect=''){
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
