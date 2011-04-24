<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_lier_contact_charger_dist($id_organisation, $redirect=''){
	$valeurs = array(
		'recherche_contact' => '',
		'id_organisation' => intval($id_organisation),
		'redirect' => $redirect
	);
	return $valeurs;
}

function formulaires_lier_contact_verifier_dist($id_organisation, $redirect=''){
	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts
	return $erreurs;
}

function formulaires_lier_contact_traiter_dist($id_organisation, $redirect=''){
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
