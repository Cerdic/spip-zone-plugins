<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if(is_array($GLOBALS['spip_matrice'])){
	$GLOBALS['spip_matrice']['image_rgb2hsv'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_hsv2rgb'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_estampage_alpha'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_saturer'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_niveaux_gris_auto'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_podpod'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_courbe'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_float'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_contour_alpha'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_sincity'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_dispersion'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_rgb2hsl'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_hue2rgb'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_hsl2rgb'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_reflechir'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_negatif'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_monochrome'] = 'filtres/images_complements.php';
	$GLOBALS['spip_matrice']['image_merge'] = 'filtres/images_complements.php';

	$GLOBALS['spip_matrice']['couleur_chroma'] = 'filtres/couleurs_complements.php';
	$GLOBALS['spip_matrice']['couleur_saturer'] = 'filtres/couleurs_complements.php';
	$GLOBALS['spip_matrice']['couleur_tableau_chroma'] = 'filtres/couleurs_complements.php';
	$GLOBALS['spip_matrice']['couleur_teinter'] = 'filtres/couleurs_complements.php';
	$GLOBALS['spip_matrice']['couleur_inverserluminosite'] = 'filtres/couleurs_complements.php';
	$GLOBALS['spip_matrice']['couleur_foncerluminosite'] = 'filtres/couleurs_complements.php';
	$GLOBALS['spip_matrice']['couleur_eclaircirluminosite'] = 'filtres/couleurs_complements.php';
	$GLOBALS['spip_matrice']['couleur_melanger'] = 'filtres/couleurs_complements.php';
	$GLOBALS['spip_matrice']['couleur_hexa_to_dec'] = 'filtres/couleurs_complements.php';
}

?>
