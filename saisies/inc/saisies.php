<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Une librairie pour manipuler ou obtenir des infos sur un tableau de saisies

/*
 * Prend la description complète du contenu d'un formulaire et retourne
 * uniquement les saisies.
 *
 * @param array $contenu : le contenu d'un formulaire
 * @return array : un tableau avec uniquement les saisies
 */
function saisies_recuperer_saisies($contenu){
	$saisies = array();
	
	if (is_array($contenu)){
		foreach ($contenu as $ligne){
			if (is_array($ligne)){
				if (array_key_exists('saisie', $ligne)){
					$saisies[$ligne['options']['nom']] = $ligne;
				}
				elseif (array_key_exists('groupe', $ligne)){
					$saisies = array_merge($saisies, saisies_recuperer_saisies($ligne['contenu']));
				}
			}
		}
	}
	
	return $saisies;
}

/*
 * Prend la description complète du contenu d'un formulaire et retourne
 * une liste des noms des champs du formulaire.
 *
 * @param array $contenu : le contenu d'un formulaire
 * @return array : un tableau listant les noms des champs
 */
function saisies_recuperer_champs($contenu){
	$saisies = saisies_recuperer_saisies($contenu);
	return array_keys($saisies);
}

?>
