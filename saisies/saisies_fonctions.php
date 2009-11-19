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
	
	// Si le parametre n'est pas bon, on genere du vide
	if (!is_array($champ))
		return '';
	
	$contexte = array();
	
	// On sélectionne le type de saisie
	$contexte['type_saisie'] = $champ['saisie'];
	
	// Peut-être des transformations à faire sur les options textuelles
	$options = $champ['options'];
	foreach ($options as $option => $valeur){
		$options[$option] = saisies_transformer_langue($valeur);
	}
	
	// On ajoute les options propres à la saisie
	$contexte = array_merge($contexte, $options);
	
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

// Applique eventuellement certaines transformations de langue a une valeur
function saisies_transformer_langue($valeur){
	// Si la valeur est bien une chaine (et pas non plus un entier déguisé)
	if (is_string($valeur) and !intval($valeur)){
		// Si la chaine commence par lang: on passe à _T()
		if (strpos($valeur, 'lang:') === 0)
			$valeur = _T(substr($valeur, 5));
		// Si la chaine contient du <multi> on appele typo() pour transformer
		elseif (strpos($valeur, '<multi>') !== false)
			$valeur = typo($valeur);
	}
	// Sinon si c'est un tableau, on fait les memes tests pour chaque valeur
	elseif (is_array($valeur)){
		foreach ($valeur as $cle => $valeur2){
			$valeur[$cle] = saisies_transformer_langue($valeur2);
		}
	}
	
	return $valeur;
}

?>
