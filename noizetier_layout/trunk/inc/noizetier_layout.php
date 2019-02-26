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
	static $grille;
	if ($grille and isset($grille[$info])) {
		return $grille[$info];
	} else if ($grille) {
		return $grille;
	}

	$grille = $retour = array();
	if (
		noizetier_layout_grille()
		and $decrire_grille = charger_fonction('decrire_grille', 'grille/'._NOIZETIER_GRILLE)
	) {
		$grille = $decrire_grille();
		// Un coup pour les plugins
		$grille = pipeline(
			'noizetier_decrire_grille',
			array(
				'args' => array(
					'grille' => _NOIZETIER_GRILLE,
				),
				'data' => $grille,
			)
		);
		// Retourner tout ou partie
		if ($info and isset($grille[$info])) {
			$retour = $grille[$info];
		} else {
			$retour = $grille;
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
	} else if ($saisies[$id_noisette]) {
		return $saisies[$id_noisette];
	}

	$saisies = $retour = array();
	if (
		noizetier_layout_grille()
		and $lister_saisies = charger_fonction('lister_saisies', 'grille/'._NOIZETIER_GRILLE)
	) {
		$saisies_grille = $lister_saisies($id_noisette);
		// Un coup pour les plugins
		$saisies_grille = pipeline(
			'noizetier_lister_saisies_grille',
			array(
				'args' => array(
					'grille'      => _NOIZETIER_GRILLE,
					'id_noisette' => $id_noisette,
				),
				'data' => $saisies_grille,
			)
		);
		// On encapsule le tout dans des fieldsets
		foreach(array('container', 'row', 'column') as $item) {
			if (isset($saisies_grille[$item])) {
				$saisies[$id_noisette][$item] = array(
					array(
						'saisie' => 'fieldset',
						'options' => array(
							'nom' => 'grille_'.$item,
							'label' => _T('noizetier_layout:grid_'.$item.'_legend'),
							'pliable' => 'oui',
						),
						'saisies' => $saisies_grille[$item],
					),
				);
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

	foreach($saisies_par_nom as $champ => $saisie) {
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
	if ($creer_classe_media = charger_fonction('creer_classe_media', 'grille/'._NOIZETIER_GRILLE, true)) {
		$classe_media = $creer_classe_media($classe, $media);
	}

	return $classe_media;
}



/**
 * Détecter à quel élément de la grille correspond une noisette
 *
 * - Si la noisette est à la racine ou dans un conteneur lambda : container
 * - Si c'est une ligne : row
 * - Si elle est dans un ligne : column
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

	$elements       = array();
	$noisette       = sql_fetsel('type_noisette,id_conteneur', 'spip_noisettes', 'id_noisette='.intval($id_noisette));
	$type_noisette  = $noisette['type_noisette'];
	$id_conteneur   = $noisette['id_conteneur'];
	$a_la_racine    = (strpos($id_conteneur, '/') !== false);
	$dans_conteneur = (strpos($id_conteneur, 'noisette') !== false);
	list($type_noisette_parente, $noisette_parente, $id_noisette_parente) = explode('|', $id_conteneur); // pas de fonction dans l'API pour avoir ces infos

	// Toutes les noisettes ont une option container.
	// C.a.d qu'elles ne sont jamais directement un container elles-mêmes,
	// mais elles peuvent en avoir un à l'intérieur.
	// Cependant pour simplifier, on n'active l'option que pour celles à la racine et celles dans un conteneur lambda (mais ça pourrait changer).
	if (
		$a_la_racine
		or $type_noisette_parente == 'conteneur'
	) {
		$elements[] = 'container';
	}

	// Noisette « conteneur_row » = row
	if ($type_noisette == 'conteneur_row') {
		$elements[] = 'row';
	}

	// Noisette enfante d'une noisette « conteneur_row » = column
	else if ($type_noisette_parente == 'conteneur_row') {
		$elements[] = 'column';
	}

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
function noizetier_layout_extraire_classes_saisies_grille($saisies){

	include_spip('inc/saisies');
	$classes = array();

	// S'il s'agit d'une saisie unique, encapsuler
	reset($saisies);
	if (!is_numeric(key($saisies))) {
		$saisies = array($saisies);
	}
	// On met tout à plat et on parse
	$saisies = saisies_lister_par_nom($saisies);
	foreach($saisies as $champ => $saisie) {
		if ($saisie['saisie'] != 'fieldset') {
			// Soit l'info est dans grille/data
			if (!empty($saisie['grille']['data'])) {
				$classes = array_merge($classes, $saisie['grille']['data']);
			// Soit dans options/data (radios, checkbox, etc.)
			} else if (!empty($saisie['options']['data'])) {
				$classes = array_merge($classes, array_keys($saisie['options']['data']));
			}
		}
	}
	$classes = array_filter($classes);

	return $classes;
}