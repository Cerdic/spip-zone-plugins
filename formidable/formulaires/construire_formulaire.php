<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_construire_formulaire_charger($formulaire_initial=array()){
	$contexte = array();
	
	// On vérifie ce qui a été passé en paramètre 
	if (!is_array($formulaire_initial)) $formulaire_initial = array();
	
	// On passe ça pour l'affichage
	$contexte['_contenu'] = $formulaire_initial;
	
	// La liste des saisies
	include_spip('inc/saisies');
	$saisies_disponibles = saisies_lister_disponibles();
	var_dump($saisies_disponibles);
	$contexte['saisies_disponibles'] = $saisies_disponibles;
	
	return $contexte;
}

function formulaires_construire_formulaire_verifier($formulaire_initial=array()){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_construire_formulaire_traiter($formulaire_initial=array()){
	$retours = array();
	
	return $retours;
}

?>
