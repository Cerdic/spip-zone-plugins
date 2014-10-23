<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2013 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_DIR_THEMES'))
	define('_DIR_THEMES', _DIR_RACINE . "themes/");

if (!defined('_DIR_THEMES_DIST'))
	define('_DIR_THEMES_DIST', _DIR_RACINE . "themes-dist/");

function zengarden_charge_themes($dir = _DIR_THEMES, $tous = false, $force = false){
	$themes = array();

	include_spip('inc/plugin');
	$files = liste_plugin_files($dir);

	if (count($files)) {
		$get_infos = charger_fonction('get_infos','plugins');

		$t = $get_infos($files,$force,$dir);
		$themes = array();

		foreach($files as $d){
			if (isset($t[$d])){
				if (isset($t[$d]['categorie']) and $t[$d]['categorie']=='theme'
				  AND ($tous OR $t[$d]['etat']=='stable')){
					$t[$d]['tri'] = strtolower(basename($d));
					$themes[substr($dir.$d,strlen(_DIR_RACINE))] = $t[$d];
				}
			}
			unset($t[$d]);
		}
	}

	return $themes;
}

?>
