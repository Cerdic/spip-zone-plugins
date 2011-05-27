<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction qui calcule le tableau des plugins ou paquets compatibles avec une version de spip
 * Cette fonction est appelee par le critere compatibilite_spip
 * @return 
 */
function svp_lister_compatibles($version, $table, $pkey) {

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
