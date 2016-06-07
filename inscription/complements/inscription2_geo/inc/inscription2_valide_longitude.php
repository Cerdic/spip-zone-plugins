<?php
/**
 *
 * Fonction de validation d'une longitude
 *
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte,
 * un message d'erreur dans le cas contraire
 * @param string $longitude La longitude testée
 * @param int $id_auteur[optional]
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inc_inscription2_valide_longitude_dist($longitude, $id_auteur = null) {
	if (!$longitude) {
		return;
	} elseif ((!is_numeric($longitude)) or ($longitude < -180) or ($longitude > 180)) {
		// verifier que la longitude soit valide
		return _T('i2_geo:saisir_longitude_valide');
	}
	return;
}
