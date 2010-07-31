<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_lier_organisation_charger_dist($id_contact){
	$valeurs = array(
		'recherche_organisation' => '',
		'id_contact' => intval($id_contact)
	);
	return $valeurs;
}

function formulaires_lier_organisation_verifier_dist($id_contact){
	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts
	return $erreurs;
}

function formulaires_lier_organisation_traiter_dist($id_contact){
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
