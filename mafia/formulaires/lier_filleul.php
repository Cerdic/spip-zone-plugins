<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_lier_filleul_charger_dist($id_filleul, $redirect=''){
	$valeurs = array(
		'recherche_auteur' => '',
		'id_auteur' => intval($id_filleul),
		'redirect' => $redirect
	);
	return $valeurs;
}

function formulaires_lier_filleul_verifier_dist($id_filleul, $redirect=''){
	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts
	return $erreurs;
}

function formulaires_lier_filleul_traiter_dist($id_filleul, $redirect=''){
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
