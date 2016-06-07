<?php
/**
 *
 * Fonction de validation d'une latitude
 *
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte,
 * un message d'erreur dans le cas contraire
 * @param string $latitude La latitude testée
 * @param int $id_auteur[optional]
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inc_inscription2_valide_latitude_dist($latitude, $id_auteur = null) {
	if (!$latitude) {
		return;
	} elseif ((!is_numeric($latitude)) or ($latitude < -90) or ($latitude > 90)) {
		// verifier que la latitude soit valide
		return _T('i2_geo:saisir_latitude_valide');
	}
	return;
}
