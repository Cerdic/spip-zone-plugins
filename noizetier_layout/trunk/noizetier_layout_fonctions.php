<?php
/**
 * Fonctions utiles au plugin Noizetier : agencements
 *
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
 * Détermine si une grille CSS est activée,
 * et si oui renvoie son identifiant.
 *
 * @return string|bool
 *    Identifiant de la grille active
 *    false si aucune
 */
function noizetier_layout_grille() {

	static $grille;
	if (!is_null($grille)) {
		return $grille;
	}

	// Todo : vérifier aussi la présence des fonctions nécessaires ?
	if (defined('_NOIZETIER_GRILLE')) {
		$grille = _NOIZETIER_GRILLE;
	} else {
		$grille = false;
	}

	return $grille;
}


/**
 * Filtre : description de la grille
 *
 * @filtre
 * @see noizetier_layout_decrire_grille()
 */
function filtre_noizetier_layout_decrire_grille_dist($info = ''){

	include_spip('inc/noizetier_layout');
	$grille = noizetier_layout_decrire_grille($info);

	return $grille;
}


/**
 * Filtre : créer la variante d'une classe pour un média
 *
 * @filtre
 * @see noizetier_layout_classe_media()
 */
function filtre_noizetier_layout_creer_classe_media_dist($classe, $media) {

	include_spip('inc/noizetier_layout');
	$classe_media = noizetier_layout_creer_classe_media($classe, $media);

	return $classe_media;
}