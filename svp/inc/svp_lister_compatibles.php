<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction qui calcule le tableau des plugins ou paquets
 * compatibles avec une version de spip.
 * Cette fonction est appelee par le critere {compatible_spip}
 * @return 
 */
function inc_svp_lister_compatibles($version, $table, $pkey) {

	// version explicite dans l'appel du critere
	// mais inexistante (#GET{vers} non declare par exemple)
	if (!strlen($version)) {
		$version = $GLOBALS['spip_version_branche'];
	}

	include_spip("inc/plugin");

	$rows = sql_allfetsel($pkey.',compatibilite_spip', 'spip_'.$table, $pkey.'>0');
	$result = array();
	foreach ($rows as $_row) {
		if (plugin_version_compatible($_row['compatibilite_spip'], $version)) {
			$result[] = $_row[$pkey];
		}
	}

	return implode($result, ',');
}

?>
