<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de base de l'API de normalisation.
 * (En cours de dev... sera peut etre a revoir)
 * 
 */
function inc_normaliser_dist($valeur, $type, $options=null){

	// On vérifie que les options sont bien un tableau
	if (!is_array($options))
		$options = array();

	$erreur = '';
	
	// On cherche si une fonction correspond au type existant
	if ($normaliser = charger_fonction($type, 'normaliser', true)) {
		$valeur_normalisee = $normaliser($valeur, $options, $erreur);
	}

	return array(
		'erreur' => $erreur,
		'valeur' => $valeur_normalisee,
		'changement' => !$erreur and ($valeur_normalisee != $valeur)
	);
}


?>
