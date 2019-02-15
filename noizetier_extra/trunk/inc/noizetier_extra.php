<?php
/**
 * Fonctions utiles au plugin Noizetier : compléments
 *
 * @plugin    Noizetier : compléments
 * @copyright 2019
 * @author    Mukt
 * @licence   GNU/GPL
 * @package   SPIP\Noizetier_complements\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Lister les saisies pour les classes liées à un type de noisette.
 *
 * Permet aux plugins de rajouter des saisies afin de manipuler les classes des noisettes de façon user-friendly, sans avoir à mémoriser des noms barbares.
 *
 * Il s'agit forcément de saisies avec des valeurs prédéfinies : des selects ou des checkbox par exemple.
 *
 * @param string $type_noisette
 * @return array
 *     Tableau associatif : type de noisette => saisies
 *     clé '*' pour tous les types de noisettes.
 */
function noizetier_lister_saisies_classes($type_noisette) {

	$classes = array();
	$classes_pipeline = pipeline('noizetier_lister_saisies_classes', array());

	// D'abord les classes spécifiques au type de noisette
	if (!empty($classes_pipeline[$type_noisette])) {
		$classes = array_merge($classes, $classes_pipeline[$type_noisette]);
	}

	// Puis les classes génériques
	if (!empty($classes_pipeline['*'])) {
		$classes = array_merge($classes, $classes_pipeline['*']);
	}

	return $classes;
}