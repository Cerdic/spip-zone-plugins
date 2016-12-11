<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Vérifier un upload d'image unique ou multiple
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
	include_spip('inc/filtres');
	$verifier = charger_fonction('verifier', 'inc', true);

	// cas des champs multiples
	if (is_array($valeur['tmp_name'])) {
		$erreurs = array();
		foreach ($valeur['tmp_name'] as $id_file => $tmp_name) {
			$fichier = array();
			foreach ($valeur as $key => $val) {
				$fichier[$key] = $valeur[$key][$id_file];
			}
			if ($erreur = $verifier($fichier, 'image_upload', $options)) {
				$valeur_normalisee[$id_file] = $erreur;
				$erreurs[]                   = $erreur;
			}
		}

		return join('<br>', $erreurs);
	}
	// cas des champs uniques
	else {
		return $verifier($valeur, 'image_upload', $options);
	}

}
