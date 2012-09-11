<?php

/**
 * Déclarations des fonctions pour les squelettes
 * @package SPIP\Elements\Fonctions
 */

// Securite
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Retourne un tableau de description de tous les éléments
 * autorisés par la configuration, classés par nom
 *
 * @return array
 *     Liste des éléments
**/
function lister_elements_autorises_par_nom() {
	$elems = lister_elements_par_nom();
	$autorises = lire_config('elements/elements', array());
	$elems = array_intersect_key($elems, array_flip($autorises));
	return $elems;
}


/**
 * Retourne un tableau de description de tous les éléments connus 
 * classés par nom
 *
 * @param string $nom
 *     Éventuel nom d'élément dont on voudrait la description uniquement
 * @return array|null
 *     - Liste des éléments
 *     - Ou seulement l'élément indiqué par $nom
 *     - null si l'élément n'est pas trouvé.
**/
function lister_elements_par_nom($nom = '') {
	$elem = elements_lister_descriptions();
	if ($nom) {
		if (isset($elem[$nom])) {
			return $elem[$nom];
		} else {
			return null;
		}
	}
	ksort($elem);
	return $elem;
}


/**
 * Obtenir les infos de tous les éléments disponibles dans les dossiers elements/
 * C'est un GROS calcul lorsqu'il est a faire.
 *
 * @note Inspiré de noizetier_obtenir_infos_noisettes_direct()
 * 
 * @return array
 */
function elements_lister_descriptions(){
	static $liste_elements = array();

	// deja calculé ?
	if ($liste_elements) {
		return $liste_elements;
	}

	$match = "[^-]*[.]html$";
	$liste = find_all_in_path('elements/', $match);

	if (count($liste)){
		foreach($liste as $squelette=>$chemin) {
			$element = preg_replace(',[.]html$,i', '', $squelette);
			$dossier = str_replace($squelette, '', $chemin);
			// On ne garde que les squelettes ayant un fichier YAML de config
			if (file_exists("$dossier$element.yaml")
				AND ($infos_element = elements_decrire_yaml($dossier.$element))
			){
				$infos_element['element'] = $element; // ajout du type d'élement dans la description
				$liste_elements[$element] = $infos_element;
			}
		}
	}

	// supprimer de la liste les noisettes necissant un plugin qui n'est pas actif
	foreach ($liste_elements as $element => $infos_element)
		if (isset($infos_element['necessite']))
			foreach ($infos_element['necessite'] as $plugin)
				if (!defined('_DIR_PLUGIN_'.strtoupper($plugin)))
					unset($liste_elements[$element]);

	return $liste_elements;
}



/**
 * Charger les informations contenues dans le YAML d'un élément
 *
 * @note Inspiré de noizetier_charger_infos_noisette_yaml()
 * 
 * @param string $element Chemin du fichier d'élément
 * @param string $info    Information a obtenir (sinon prend tout)
 * @return array|string   Liste des informations
 */
function elements_decrire_yaml($element, $info=""){
		// on peut appeler avec le nom du squelette
		$fichier = preg_replace(',[.]html$,i','',$element).".yaml";
		include_spip('inc/yaml');
		include_spip('inc/texte');
		$infos_element = array();
		if ($infos_element = yaml_charger_inclusions(yaml_decode_file($fichier))) {
			if (isset($infos_element['nom']))
				$infos_element['nom'] = _T_ou_typo($infos_element['nom']);
			if (isset($infos_element['description']))
				$infos_element['description'] = _T_ou_typo($infos_element['description']);
			if (isset($infos_element['icon']))
				$infos_element['icon'] = $infos_element['icon'];

			if (!isset($infos_element['parametres']))
				$infos_element['parametres'] = array();

			// contexte
			if (!isset($infos_element['contexte'])) {
				$infos_element['contexte'] = array();
			}
			if (is_string($infos_element['contexte'])) {
				$infos_element['contexte'] = array($infos_element['contexte']);
			}

			// ajax
			if (!isset($infos_element['ajax'])) {
				$infos_element['ajax'] = 'oui';
			}
			// inclusion
			if (!isset($infos_element['inclusion'])) {
				$infos_element['inclusion'] = 'statique';
			}
		}

		if (!$info)
			return $infos_element;
		else 
			return isset($infos_element[$info]) ? $infos_element[$info] : "";
}


/**
 * À partir des infos d'éléments stockés en bdd dans spip_elements/elements
 * retrouve la description yaml des éléments correspondants 
 *
 * @param array $elements
 *     Liste d'elements sélectionnés tels qu'enregistrés dans la bdd
 *     mais désélialisés
 * @retrun array
 *     Liste dans le même ordre des éléments avec leurs description yaml
 * 
**/
function elements_obtenir_desriptions($elements) {
	$descriptions = array();
	$liste = lister_elements_par_nom();
	// pour chaque element choisi, retrouve la description yaml du type d'element
	foreach ($elements as $k=>$e) {
		$type = $e['element'];
		if (isset($liste[$type])) {
			$descriptions[$k] = $liste[$type];
		}
	}
	return $descriptions;
}


/**
 * Retourne le code HTML d'un élément
 *
 * @param string $element
 *     Nom de l'élément
 * @param array $args
 *     Valeurs enregistrées dans la base pour l'élément.
 *     L'index 'contexte' contient le contexte transmis à l'inclusion
 * @return string
 *     Code HTML de l'élément
**/
function elements_inclure_element($element, $args=array()) {
	if (!$element) {
		return '';
	}
	
	$contexte = $args['contexte'];
	$contexte['_element'] = $args['element'];
	$code = recuperer_fond("elements/$element", $contexte);
	return $code;
}


/**
 * Retourne le code HTML du titre d'un élément
 *
 * Le titre est défini par un squelette elements/{element}_titre.html
 * lorsqu'il est présent. Sinon laissé vide.
 * 
 * @param string $element
 *     Nom de l'élément
 * @param array $args
 *     Valeurs enregistrées dans la base pour l'élément.
 *     L'index 'contexte' contient le contexte transmis à l'inclusion
 * @return string
 *     Code HTML du titre de l'élément, sinon rien.
**/
function elements_inclure_element_titre($element, $args=array()) {
	if (!$element) {
		return '';
	}
	$element_titre = $element . '_titre';

	if (!trouver_fond($element_titre, 'elements')) {
		return '';
	}

	$contexte = $args['contexte'];
	$contexte['_element'] = $args['element'];
	$code = recuperer_fond("elements/$element_titre", $contexte);
	return $code;
}
