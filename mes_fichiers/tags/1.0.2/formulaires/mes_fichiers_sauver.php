<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/autoriser');

function formulaires_mes_fichiers_sauver_charger_dist(){
	include_spip('inc/mes_fichiers_utils');
	$valeurs = array();
	$valeurs['_fichiers'] = mes_fichiers_a_sauver();
	if(!autoriser('sauvegarder','mes_fichiers')) {
		$valeurs['editable'] = false;
	}
	return $valeurs;
}

function formulaires_mes_fichiers_sauver_verifier_dist(){

	return $erreurs;
}

function formulaires_mes_fichiers_sauver_traiter_dist(){
	$sauver = charger_fonction('mes_fichiers_sauver','action');
	$erreur = $sauver();
	if($erreur){
		$res['message_erreur'] = _T('mes_fichiers:message_sauvegarde_nok');
	}else{
		$res['message_ok'] = _T('mes_fichiers:message_sauvegarde_ok');
	}
	return $res;
}

?>
