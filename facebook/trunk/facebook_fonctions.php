<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ce filtre va afficher l'image de profil facebook :
 *
 * ```
 * [(#TOKEN_FACEBOOK|facebook_profil_picture{300,300, Image facebook})]
 * ```
 *
 * @param mixed $token Token Facebook
 * @param int $width Largeur de l'image à demander à Facebook
 * @param int $height Hauteur de l'image à demander à Facebook
 * @param string $alt attribut alt de l'image
 * @param string $class class de l'image
 * @access public
 * @return string Une balise img
 */
function filtre_facebook_profil_picture_dist($token, $width = 0, $height = 0, $alt = '', $class = '') {
	include_spip('inc/facebook');
	$picture = facebook_profil_picture($token, $width, $height);
	$balise_img = charger_filtre('balise_img');
	if (!empty($picture['url'])) {
		return $balise_img($picture['url'], $alt, $class);
	} else {
		return false;
	}
}
