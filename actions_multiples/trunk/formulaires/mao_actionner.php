<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_mao_actionner_charger_dist($liste, $objet) {
	$valeurs = array();

	$valeurs['liste'] = $liste;
	$valeurs['objet'] = $objet;
	$ids = _request("ids_${objet}");
	$valeurs["ids_${objet}"] =  $ids ? $ids : array();

	return $valeurs;
}


function formulaires_mao_actionner_verifier_dist($liste, $objet) {
	$erreurs = array();

	return $erreurs;
}


function formulaires_mao_actionner_traiter_dist($liste, $objet) {
	$retour = array();

	$ids = _request("ids_${objet}");

	$retour['message_ok'] = 'ok';
	$retour['editable'] = true;

	return $retour;
}
