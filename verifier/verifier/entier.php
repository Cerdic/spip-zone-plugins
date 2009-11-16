<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Verifie qu'un entier coherent peut etre extrait de la valeur
 * Options :
 * - min : valeur minimale acceptee
 * - max : valeur maximale acceptee
 */
function verifier_entier_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_entier');
	
	// Pas de tableau ni d'objet
	if (is_numeric($valeur)){
		// Si c'est une chaine on convertit en entier et si c'est un flottant on ne garde que l'entier
		$valeur = intval($valeur);
		$ok = true;
		$erreur = '';
		
		if (isset($options['min']))
			$ok = ($ok and ($valeur >= $options['min']));
		
		if (isset($options['max'])){
			$ok = ($ok and ($valeur <= $options['max']));
		}
		
		if (!$ok){
			if (isset($options['min']) and isset($options['max']))
				$erreur = _T('verifier:erreur_entier_entre', $options);
			elseif (isset($options['max']))
				$erreur = _T('verifier:erreur_entier_max', $options);
			else
				$erreur = _T('verifier:erreur_entier_min', $options);
		}
	}
	
	return $erreur;
}

?>
