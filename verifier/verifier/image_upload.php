<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Vérifier un upload d'image unique
 * Cette fonction est depréciée, on utilisera de préférence la vérification 'fichiers', plus souple, et à laquelle la présente fonction renvoie. 
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
	// Convertir pour les nouveaux réglages de la vérification 'fichier'
	$nouvelles_options = array('mime'=>'image_web');
	if (isset($options['taille_max'])) {
		$nouvelles_options['taille_max'] = $options['taille_max'];
	}
	if (isset($options['largeur_max']) or isset($options['hauteur_max'])) {
		$nouvelles_options['dimension_max'] = array();
		if (isset($options['largeur_max'])) {
			$nouvelles_options['dimension_max']['largeur'] = $options['largeur_max']; 
		}
		if (isset($options['hauteur_max'])) {
			$nouvelles_options['dimension_max']['hauteur'] = $options['hauteur_max']; 
		}
	}
	$verifier = charger_fonction('verifier', 'inc', true);

	return $verifier($valeur, 'fichiers', $nouvelles_options, $valeur_normalisee);
}
