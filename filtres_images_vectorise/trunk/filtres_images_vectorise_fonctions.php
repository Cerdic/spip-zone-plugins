<?php
/**
 * Fonctions du plugin Filtres Images Vectorise
 *
 * @plugin     Filtres Images Vectorise
 * @copyright  2019
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Filtres Images Vectorise\Fonctions
 */


if (!defined('_ECRIRE_INC_VERSION')) return;

if(is_array($GLOBALS['spip_matrice'])){
	$GLOBALS['spip_matrice']['image_filtrer_couleur'] = 'filtres/image_filtrer_couleur.php';
	$GLOBALS['spip_matrice']['image_geometrize'] = 'filtres/image_geometrize.php';
	$GLOBALS['spip_matrice']['image_potrace'] = 'filtres/image_potrace.php';
	$GLOBALS['spip_matrice']['image_geopotrize'] = 'filtres/image_geopotrize.php';
}

function extraire_palette_couleurs($img, $nb_couleurs=3, $width_thumb=32) {
	include_spip('filtres/extraire_palette');
	return _image_extraire_palette($img, $nb_couleurs, $width_thumb);
}