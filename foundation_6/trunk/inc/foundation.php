<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cette fonction va créer la class foundation de la balise #COLONNE
 *
 * @param  int|array $nombre_colonnes Nombre de colonne désiré
 * @param  string $type Foundation 4/5, type de colonne (large, medium, small)
 * @return string class foundation applicable directement.
 */
function class_grid_foundation($nombre_colonnes, $type) {

	// Si la première variable est un tableau, on va le convertir en class
	if (is_array($nombre_colonnes)) {
		$class= '';
		foreach ($nombre_colonnes as $key => $value) {
			// Utiliser un tableau large => 4
			if (is_numeric($value)) {
				$class .= $key.'-'.$value.' ';
			}
		}
		return $class;
	} else {
		return $type.'-'.$nombre_colonnes.' ';
	}
}
