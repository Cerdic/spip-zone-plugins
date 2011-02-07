<?php

/**
 * Si on est en 2.1 etc...
 */
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
}
else{
	include_spip('filtres/images_complements');
	include_spip('filtres/couleurs_complements');
}

/*
 * autorise les filtres images sur les chemins.
 * #CHEMIN{fichier}|en_image|image_sepia{14579c}
 *
 * Cette fonction est devenue inutile en 1.9.3 [10980]
 *
 */
function en_image($url, $alt=''){
	return 	"<img src='". $url ."' alt='". $alt ."' />";
}

?>