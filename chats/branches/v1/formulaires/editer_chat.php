<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_chat_charger_dist($id_chat='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('chat', $id_chat, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_chat_verifier_dist($id_chat='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('chat', $id_chat, array('nom'));
	var_dump($erreurs);
	return $erreurs;
}

function formulaires_editer_chat_traiter_dist($id_chat='new', $retour=''){
	return formulaires_editer_objet_traiter('chat', $id_chat, '', '', $retour, '');
}