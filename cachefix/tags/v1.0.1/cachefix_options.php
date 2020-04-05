<?php

/**
 * Renvoie la branche SPIP sur 2 digits (exemple : 3.2)
 * telle que définie dans spip_loader
 *
 * @param $version
 * @return string
 */
function cachefix_branche_spip($version)
{
	if ($version == 'spip') {
		return 'dev';
	}
	$v = explode('.', $version);
	$branche = $v[0] . '.' . (isset($v[1]) ? $v[1] : '0');
	return $branche;
}

/**
 *
 * Inclure une version d'un fichier selon la version de SPIP
 *
 * @param $file fichier à inclure, avec le dossier, sans le php (exemple : 'inc/utils')
 *
 */
function cachefix_inclure_version($file) {
static $branche_spip;
	if (!$branche_spip)
		$branche_spip = cachefix_branche_spip ($GLOBALS['spip_version_branche']);

	if (in_array($branche_spip, array('3.0', '3.1', '3.2', '3.3')))  {
		spip_log("include ($file.fix.$branche_spip.php);", 'cachefix_version');
		include ("$file.fix.$branche_spip.php");
	}
	else
		include(_DIR_RESTREINT.$file.'.php');
}

