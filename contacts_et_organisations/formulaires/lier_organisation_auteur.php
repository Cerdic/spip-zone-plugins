<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_lier_organisation_auteur_charger_dist($id_organisation, $redirect=''){
	$valeurs = array(
		'recherche_auteur' => '',
		'id_organisation' => intval($id_organisation),
		'retour' => $redirect
	);
	return $valeurs;
}

function formulaires_lier_organisation_auteur_verifier_dist($id_organisation, $redirect=''){
	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts
	return $erreurs;
}

function formulaires_lier_organisation_auteur_traiter_dist($id_organisation, $redirect=''){
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
