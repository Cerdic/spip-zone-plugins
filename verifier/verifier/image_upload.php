<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Vérifier un upload d'image unique
 *
 * @param array $valeur
 *   Le sous tableau de $_FILES à vérifier, $_FILES['logo'] par exemple
 *   Doit être un champ avec un seul upload
 * @param array $options
 *   Options à vérifier :
 *   - taille_max (en kio)
 *   - largeur_max (en px)
 *   - hauteur_max (en px)
 *
 * @return string
 */
function verifier_image_upload_dist($valeur, $options) {
	include_spip('inc/filtres');

	// vérifier le type
	if ($valeur['type'] && !preg_match('#^image#', $valeur['type'])) {
		return _T('verifier:erreur_type_image', array('name' => $valeur['name']));
	}

	// vérifier le poids
	$taille_max = 1024 * (isset($options['taille_max']) ? $options['taille_max'] : (defined('_IMG_MAX_SIZE') ? _IMG_MAX_SIZE : 0));
	if ($taille_max) {
		$taille = ($valeur['size'] ? $valeur['size'] : @filesize($valeur['tmp_name']));
		if ($taille > $taille_max) {
			return _T('verifier:erreur_taille_image', array(
				'name'       => $valeur['name'],
				'taille_max' => taille_en_octets($taille_max),
				'taille'     => taille_en_octets($taille)
			));
		}
	}

	// vérifier les dimensions
	if ($imagesize = @getimagesize($valeur['tmp_name']) and (isset($options['largeur_max']) or defined('_IMG_MAX_WIDTH') or isset($options['hauteur_max']) or defined('_IMG_MAX_HEIGHT'))) {
		$largeur_max = (isset($options['largeur_max']) ? $options['largeur_max'] : (defined('_IMG_MAX_WIDTH') ? _IMG_MAX_WIDTH : 0));
		$hauteur_max = (isset($options['hauteur_max']) ? $options['hauteur_max'] : (defined('_IMG_MAX_HEIGHT') ? _IMG_MAX_HEIGHT : 0));
		if ($imagesize[0] > $largeur_max || $imagesize[1] > $hauteur_max) {
			return _T('verifier:erreur_dimension_image', array(
				'name'       => $valeur['name'],
				'taille_max' => $largeur_max . 'x' . $hauteur_max,
				'taille'     => $imagesize[0] . 'x' . $imagesize[1]
			));
		}
	}

	return '';
}
