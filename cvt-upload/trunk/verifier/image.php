<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Vérifier un upload d'image
 *
 * @param string $valeur
 *   Le sous tableau de $_FILES à vérifier, $_FILES['logo'] par exemple
 * @param array  $options
 *   Options à vérifier :
 *   - taille_max (en Ko)
 *   - largeur_max (en px)
 *   - hauteur_max (en px) *
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */

function verifier_image_dist($valeur, $options = array()) {
	include_spip('inc/filtres');

	if($valeur['tmp_name'] && isset($valeur['type']) && !preg_match('#^image#',$valeur['type'])){
		return _T('cvtupload:erreur_type_image');
	}
	
	$taille_max = 1024 * (isset($options['taille_max']) ? $options['taille_max'] : (defined('_IMG_MAX_SIZE') ? _IMG_MAX_SIZE : 0));
	if ($taille_max) {
		$taille = (isset($valeur['size']) ? $valeur['size'] : @filesize($valeur['tmp_name']));
		if ($taille > $taille_max) {
			return _T('cvtupload:erreur_taille_image', array(
				'taille_max' => taille_en_octets($taille_max),
				'taille'     => taille_en_octets($taille)
			));
		}
	}
	
	if ($imagesize = @getimagesize($valeur['tmp_name'])) {
		$largeur_max = (isset($options['largeur_max']) ? $options['largeur_max'] : (defined('_IMG_MAX_WIDTH') ? _IMG_MAX_WIDTH : 0));
		$hauteur_max = (isset($options['hauteur_max']) ? $options['hauteur_max'] : (defined('_IMG_MAX_HEIGHT') ? _IMG_MAX_HEIGHT : 0));
		if ($imagesize[0] > $largeur_max || $imagesize[1] > $hauteur_max) {
			return _T('cvtupload:erreur_dimension_image', array(
				'taille_max' => $largeur_max . 'x' . $hauteur_max,
				'taille'     => $imagesize[0] . 'x' . $imagesize[1]
			));
		}
	}

	return '';
}
