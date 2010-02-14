<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Une librairie pour manipuler ou obtenir des infos sur un tableau de saisies

/*
 * Prend la description complète du contenu d'un formulaire et retourne
 * uniquement les saisies.
 *
 * @param array $contenu Le contenu d'un formulaire
 * @return array Un tableau avec uniquement les saisies
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
 * @param array $contenu Le contenu d'un formulaire
 * @return array Un tableau listant les noms des champs
 */
function saisies_recuperer_champs($contenu){
	$saisies = saisies_recuperer_saisies($contenu);
	return array_keys($saisies);
}

/*
 * Liste toutes les saisies configurables (ayant une description)
 *
 * @return array Un tableau listant des saisies et leurs options
 */
function saisies_lister_disponibles(){
	static $saisies = null;
	
	if (is_null($saisies)){
		$saisies = array();
		$liste = find_all_in_path('saisies/', '.+[.]yaml$');
		
		if (count($liste)){
			foreach ($liste as $fichier=>$chemin){
				$type_saisie = preg_replace(',[.]yaml$,i', '', $fichier);
				$dossier = str_replace($fichier, '', $chemin);
				// On ne garde que les saisies qui ont bien le HTML avec !
				if (file_exists("$dossier$type_saisie.html")
					and (
						is_array($saisie = saisies_charger_infos($type_saisie))
					)
				){
					$saisies[$type_saisie] = $saisie;
				}
			}
		}
	}
	
	return $saisies;
}

/**
 * Charger les informations contenues dans le yaml d'une saisie
 *
 * @param string $type_saisie Le type de la saisie
 * @return array Un tableau contenant le YAML décodé
 */
function saisies_charger_infos($type_saisie){
	include_spip('inc/yaml');
	$fichier = find_in_path("saisies/$type_saisie.yaml");
	$saisie = yaml_decode_file($fichier);
	if (is_array($saisie)){
		$saisie['titre'] = $saisie['titre'] ? _T_ou_typo($saisie['titre']) : $type_saisie;
		$saisie['description'] = $saisie['description'] ? _T_ou_typo($saisie['description']) : '';
		$saisie['icone'] = $saisie['icone'] ? find_in_path($saisie['icone']) : find_in_path('rien.gif');
	}
	return $saisie;
}

?>
