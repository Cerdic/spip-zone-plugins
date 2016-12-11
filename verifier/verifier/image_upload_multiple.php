<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Vérifier un upload d'image unique ou multiple
 * 
 * Cette fonction n'est conservée que pour compatibilité ascendant. 
 * Lui préferer la vérification 'fichiers', qui possède plus d'options, et qui est d'ailleurs appeler ici. 
 *
 * @param array $valeur
 *   Le sous tableau de $_FILES à vérifier
 *   Peut être un champ avec plusieurs uploads
 * @param array $options
 *   Options à vérifier :
 *   - taille_max (en kio)
 *   - largeur_max (en px)
 *   - hauteur_max (en px)
 * @param null  $valeur_normalisee
 *   Retourne un tableau des indexes de fichiers en erreur
 *
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */

function verifier_image_upload_multiple_dist($valeur, $options = array(), &$valeur_normalisee = null) {
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
