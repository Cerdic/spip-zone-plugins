<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_langonet_generer_charger(){
	$valeurs = array();
	$champs = array('module', 'langue_source', 'chemin_langue', 'langue_cible', 'mode');
	foreach($champs as $_champ){
		$valeurs[$_champ] = _request($_champ);
	}
	return $valeurs;
}

function formulaires_langonet_generer_verifier(){
	$erreurs = array();
	$obligatoires = array('module', 'langue_source', 'chemin_langue');
	foreach($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_langonet_generer_traiter(){
	// Recuperation des champs du formulaire
	$champs = array('module', 'langue_source', 'chemin_langue', 'langue_cible', 'mode', 'encodage');
	foreach($champs as $_champ){
		$champs[$_champ] = _request($_champ);
	}
	if (substr($champs['chemin_langue'],-1) != '/') {
		$champs['chemin_langue'] .= '/';
	}
	// Generation du fichier
	$langonet_generer = charger_fonction('langonet_generer_fichier','inc');
	$retour = $langonet_generer($champs['module'], $champs['langue_source'], $champs['chemin_langue'], $champs['langue_cible'], $champs['mode'], $champs['encodage']);
	$retour['editable'] = true;
	return $retour;
}

?>