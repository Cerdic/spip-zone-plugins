<?php
/**
 * Fonctions utiles au plugin Noizetier : agencements
 *
 * @api       Grille
 * @plugin    Noizetier : agencements
 * @copyright 2019
 * @author    Mukt
 * @licence   GNU/GPL
 * @package   SPIP\Noizetier_agencements\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Description de la grille : breakpoints etc.
 *
 * @param string $info
 *     Pour renvoyer une clé en particulier
 * @return array|string
 *     Tableau associatif ou chaîne si on demande un info précise (pour certaines)
 *     Tableau vide en cas d'erreur
 */
function noizetier_layout_decrire_grille($info = null) {

	// Ne pas faire plusieurs fois le travail
	static $description_grille;
	if ($description_grille and isset($description_grille[$info])) {
		return $description_grille[$info];
	} elseif ($description_grille) {
		return $description_grille;
	}

	$description_grille = $retour = array();
	if (
		$grille = noizetier_layout_grille()
		and $decrire_grille = charger_fonction('decrire_grille', 'grillecss/'.$grille, true)
	) {
		$description_grille = $decrire_grille();
		// Un coup pour les plugins
		$description_grille = pipeline(
			'noizetier_layout_decrire_grille',
			array(
				'args' => array(
					'grille' => $nom_grille,
				),
				'data' => $description_grille,
			)
		);
		// Retourner tout ou partie
		if ($info and isset($description_grille[$info])) {
			$retour = $description_grille[$info];
		} else {
			$retour = $description_grille;
		}
	}

	return $retour;
}


/**
 * Description des saisies relatives à la grille pour l'édition d'une noisette
 *
 * @param string $element
 *     Indique de quel type d'élément il s'agit : container | row | column
 * @param int $id_noisette
 *     N° d'une noisette (optionnel)
 * @return array
 *     Description des saisies pour l'élément demandé
 */
function noizetier_layout_lister_saisies($element = null, $id_noisette = 0) {

	// Ne pas faire plusieurs fois le travail
	static $saisies;
	if ($saisies and isset($saisies[$id_noisette][$element])) {
		return $saisies[$id_noisette][$element];
	} elseif ($saisies[$id_noisette]) {
		return $saisies[$id_noisette];
	}

	$saisies = $retour = array();
	if (
		$grille = noizetier_layout_grille()
		and $lister_saisies = charger_fonction('lister_saisies', 'grillecss/'.$grille, true)
	) {
		$saisies_grille = $lister_saisies($id_noisette);
		// Un coup pour les plugins
		$saisies_grille = pipeline(
			'noizetier_layout_lister_saisies_grille',
			array(
				'args' => array(
					'grille'      => $nom_grille,
					'id_noisette' => $id_noisette,
				),
				'data' => $saisies_grille,
			)
		);
		// On ajoute les saisies
		foreach (array('container', 'row', 'column', '*') as $item) {
			if (isset($saisies_grille[$item])) {
				switch ($item) {
					// Celles directement à la racine
					// (pour l'instant, toutes)
					case 'container':
					case 'row':
					case 'column':
					case '*':
						$saisies[$id_noisette][$item] = $saisies_grille[$item];
						break;
					// Les autres dans un fieldset
					default:
						$saisies[$id_noisette][$item] = array(
							array(
								'saisie' => 'fieldset',
								'options' => array(
									'nom' => 'grille_'.$item,
									'label' => _T('noizetier_layout:grid_'.$item.'_legend'),
									'pliable' => 'oui',
									// 'plie' => 'oui',
								),
								'saisies' => $saisies_grille[$item],
							),
						);
						break;
				}
			}
		}
		// Retourner tout ou partie
		if ($element and isset($saisies[$id_noisette][$element])) {
			$retour = $saisies[$id_noisette][$element];
		} else {
			$retour = $saisies[$id_noisette];
		}
	}

	return $retour;
}


/**
 * Identifie les classes d'un noisette qui correspondent à la grille, et retourne un contexte
 *
 * @param string $element
 *     Type d'élément de la grille : container | row | column
 * @param string $classes_element
 *     Classes attribuées à l'élément
 * @return array
 *     Tableau associatif champ => valeur
 */
function noizetier_layout_contextualiser_classes($element, $classes_element, $id_noisette = 0) {

	include_spip('inc/saisies');
	$contexte        = array();
	$saisies         = noizetier_layout_lister_saisies($element, $id_noisette);
	$classes_element = array_filter(explode(' ', $classes_element));
	$saisies_par_nom = saisies_lister_par_nom($saisies);

	foreach ($saisies_par_nom as $champ => $saisie) {
		if (
			isset($saisie['grille'])
			and $saisie['saisie'] != 'fieldset'
		) {
			// Soit la valeur postée
			if (_request($champ)) {
				$contexte[$champ] = _request($champ);
			// Soit retrouver la valeur d'après la classe
			} else {
				$classes_champ = noizetier_layout_extraire_classes_saisies_grille($saisie);
				if ($valeur = array_intersect($classes_element, $classes_champ)) {
					// Certaines valeurs ne doivent pas être des tableaux
					if (empty($saisie['grille']['multiple'])) {
						$valeur = array_shift($valeur);
					}
					$contexte[$champ] = $valeur;
				}
			}
		}
	}

	return $contexte;
}


/**
 * Créer la variante d'une classe pour un média
 *
 * Par exemple :
 * - gridle : gr-6 => gr-6@desktop
 * - bootstrap : col-6 => col-lg-6
 *
 * @param string $classe
 *     Classe à modifier
 * @param string $media
 *     Le media
 * @return string
 *     La classe modifiée
 */
function noizetier_layout_creer_classe_media($classe, $media) {

	$classe_media = $classe;
	$grille = noizetier_layout_grille();
	if ($creer_classe_media = charger_fonction('creer_classe_media', 'grillecss/'.$grille, true)) {
		$classe_media = $creer_classe_media($classe, $media);
	}

	return $classe_media;
}



/**
 * Détecter à quel élément de la grille correspond une noisette
 *
 * - Si la noisette est à la racine ou dans un conteneur lambda : container
 * - Si c'est une ligne : row
 * - Si elle est dans une ligne : column
 *
 * @note
 * Par commodité, une noisette peut à la fois être container + row/column
 *
 * @param int $id_noisette
 *     N° de la noisette
 * @return array
 *     Tableau : container | row | column
 */
function noizetier_layout_identifier_element_grille($id_noisette) {

	include_spip('inc/config');
	$elements       = array();
	$noisette       = sql_fetsel('type_noisette,id_conteneur,profondeur', 'spip_noisettes', 'id_noisette='.intval($id_noisette));
	$type_noisette  = $noisette['type_noisette'];
	$id_conteneur   = $noisette['id_conteneur'];
	$profondeur     = $noisette['profondeur'];
	$dans_conteneur = (strpos($id_conteneur, 'noisette') !== false);
	list($type_noisette_parente, $noisette_parente, $id_noisette_parente) = explode('|', $id_conteneur); // pas de fonction dans l'API pour avoir ces infos
	$activer_container = lire_config('noizetier_layout/activer_container');

	// Toutes les noisettes peuvent techniquement avoir un .container.
	// Pour simplifier, on n'active l'option que pour celles à la racine.
	if (
		$activer_container
		and $profondeur < 1
	) {
		$elements[] = 'container';
	}

	// Noisette « conteneur » = row
	if ($type_noisette == 'conteneur') {
		$elements[] = 'row';
	}

	// Noisette enfante d'une noisette « conteneur » = column
	if ($type_noisette_parente == 'conteneur') {
		$elements[] = 'column';
	}

	// var_dump($id_noisette, $elements);

	return $elements;
}


/**
 * Récupérer la liste des classes dans les saisies de la grille
 *
 * Soit il y a la liste dans grille/data, sinon on se rabat sur options/data
 *
 * @param array $saisies
 *     - Soit une seule saisie
 *     - Soit les saisies d'un type d'élément de la grille
 * @return array
 *     Liste des classes
 */
function noizetier_layout_extraire_classes_saisies_grille($saisies) {

	include_spip('inc/saisies');
	$classes = array();

	// S'il s'agit d'une saisie unique, encapsuler
	reset($saisies);
	if (!is_numeric(key($saisies))) {
		$saisies = array($saisies);
	}
	// On met tout à plat et on parse
	$saisies = saisies_lister_par_nom($saisies);
	foreach ($saisies as $champ => $saisie) {
		if ($saisie['saisie'] != 'fieldset') {
			// Soit l'info est dans grille/data
			if (!empty($saisie['grille']['data'])) {
				$classes = array_merge($classes, $saisie['grille']['data']);
			// Soit dans options/data (radios, checkbox, etc.)
			} elseif (!empty($saisie['options']['data'])) {
				$classes = array_merge($classes, array_keys($saisie['options']['data']));
			}
		}
	}
	$classes = array_filter($classes);

	return $classes;
}