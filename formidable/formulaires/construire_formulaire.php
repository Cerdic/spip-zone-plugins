<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_construire_formulaire_charger($identifiant, $formulaire_initial=array()){
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
	
	// La liste des saisies
	include_spip('inc/saisies');
	$saisies_disponibles = saisies_lister_disponibles();
	$contexte['saisies_disponibles'] = $saisies_disponibles;
	
	return $contexte;
}

function formulaires_construire_formulaire_verifier($identifiant, $formulaire_initial=array()){
	$erreurs = array();
	
	if ($nom = _request('configurer_saisie')){
		$erreurs['configurer_'.$nom] = true;
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

// Ajouter les boutons d'actions de contrôle de chaque saisie
function formidable_ajouter_actions_saisie($html_saisie, $ajouts){
	return preg_replace('/^(<li[^>]*>)/i', '$1'.$ajouts, $html_saisie);
}

?>
