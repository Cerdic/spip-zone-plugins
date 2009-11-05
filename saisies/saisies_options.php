<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Génère une saisie à partir d'un tableau la décrivant et de l'environnement
 * Le tableau doit être de la forme suivante :
 * array(
 *		'saisie' => 'input',
 *		'options' => array(
 *			'nom' => 'le_name',
 *			'label' => 'Un titre plus joli',
 *			'obligatoire' => 'oui',
 *			'explication' => 'Remplissez ce champ en utilisant votre clavier.'
 *		)
 * )
 */
function generer_saisie($champ, $env){
	
	$contexte = array();
	
	// On sélectionne le type de saisie
	$contexte['type_saisie'] = $champ['saisie'];
	
	// On ajoute les options propres à la saisie
	$contexte = array_merge($contexte, $champ['options']);
	
	// Si env est définie dans les options, on ajoute tout l'environnement
	if(isset($contexte['env'])){
		unset($contexte['env']);
		$contexte = array_merge($env, $contexte);
	}
	// Sinon on ne sélectionne que quelques éléments importants
	else{
		// On récupère de l'environnement, la valeur actuelle du champ
		$contexte['valeur'] = $env[$contexte['nom']];
		
		// On récupère la liste des erreurs
		$contexte['erreurs'] = $env['erreurs'];
	}
	
	// On génère la saisie
	return recuperer_fond(
		'saisies/_base',
		$contexte
	);
	
}

?>
