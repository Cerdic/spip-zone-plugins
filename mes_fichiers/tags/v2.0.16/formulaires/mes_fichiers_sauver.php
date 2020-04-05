<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/mes_fichiers_utils');

function formulaires_mes_fichiers_sauver_charger_dist(){

	$valeurs = array();
	$valeurs['_fichiers'] = mes_fichiers_a_sauver();

	return $valeurs;
}

function formulaires_mes_fichiers_sauver_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}

function formulaires_mes_fichiers_sauver_traiter_dist(){
	$sauver = charger_fonction('mes_fichiers_sauver','action');
	$erreur = $sauver();

	if ($erreur) {
		$retour['message_erreur'] =
			_T('mes_fichiers:message_sauvegarde_nok') .
			" $erreur";
	}
	else {
		$retour['message_ok'] = _T('mes_fichiers:message_sauvegarde_ok');
	}

	return $retour;
}

?>
