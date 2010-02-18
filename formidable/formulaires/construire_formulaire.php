<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_construire_formulaire_charger($identifiant, $formulaire_initial=array()){
	include_spip('inc/saisies');
	$contexte = array();
	
	// On ajoute un préfixe devant l'identifiant, pour être sûr
	$identifiant = 'constructeur_formulaire_'.$identifiant;
	
	// On vérifie ce qui a été passé en paramètre 
	if (!is_array($formulaire_initial)) $formulaire_initial = array();
	
	// On initialise la session si elle est vide
	if (is_null($formulaire_actuel = session_get($identifiant))){
		session_set($identifiant, $formulaire_initial);
		$formulaire_actuel = $formulaire_initial;
	}
	
	// On passe ça pour l'affichage
	$contexte['_contenu'] = $formulaire_actuel;
	// On passe ça pour la récup plus facile des champs
	$contexte['_saisies'] = saisies_recuperer_saisies($formulaire_actuel);
	
	// La liste des saisies
	$saisies_disponibles = saisies_lister_disponibles();
	$contexte['saisies_disponibles'] = $saisies_disponibles;
	
	return $contexte;
}

function formulaires_construire_formulaire_verifier($identifiant, $formulaire_initial=array()){
	include_spip('inc/saisies');
	$erreurs = array();
	// On ajoute un préfixe devant l'identifiant
	$identifiant = 'constructeur_formulaire_'.$identifiant;
	// On récupère le formulaire à son état actuel
	$formulaire_actuel = session_get($identifiant);
	// On récupère les saisies actuelles
	$saisies_actuelles = saisies_recuperer_saisies($formulaire_actuel);
	// La liste des saisies
	$saisies_disponibles = saisies_lister_disponibles();
	
	if ($nom = _request('configurer_saisie')){
		$saisie = $saisies_actuelles[$nom];
		$form_config = $saisies_disponibles[$saisie['saisie']]['options'];
		array_walk_recursive($form_config, 'formidable_transformer_nom', "_saisies[$nom][options][@valeur@]");
		$erreurs['configurer_'.$nom] = $form_config;
	}
	
	return $erreurs;
}

function formulaires_construire_formulaire_traiter($identifiant, $formulaire_initial=array()){
	include_spip('inc/saisies');
	$retours = array();
	
	// On ajoute un préfixe devant l'identifiant
	$identifiant = 'constructeur_formulaire_'.$identifiant;
	// On récupère le formulaire à son état actuel
	$formulaire_actuel = session_get($identifiant);
	
	if ($ajouter_saisie = _request('ajouter_saisie')){
		$formulaire_actuel[] = array(
			'saisie' => $ajouter_saisie,
			'options' => array(
				'nom' => saisies_generer_nom($formulaire_actuel, $ajouter_saisie)
			)
		);
	}
	
	// On enregistre en session la nouvelle version du formulaire
	session_set($identifiant, $formulaire_actuel);
	
	// Le formulaire reste éditable
	$retours['editable'] = true;
	
	return $retours;
}

// À utiliser avec un array_walk_recursive()
// Applique une transformation à la @valeur@ de tous les champs "nom" d'un formulaire, y compris loin dans l'arbo
function formidable_transformer_nom(&$valeur, $cle, $transformation){
	if ($cle == 'nom' and is_string($valeur)){
		$valeur = str_replace('@valeur@', $valeur, $transformation);
	}
}

?>
