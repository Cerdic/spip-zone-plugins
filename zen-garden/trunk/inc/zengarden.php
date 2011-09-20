<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2011 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!defined('_DIR_THEMES'))
	define('_DIR_THEMES',_DIR_RACINE."themes/");

function zengarden_charge_themes($dir = _DIR_THEMES, $tous = false, $force = false){
	$themes = array();

	$files = preg_files($dir,"/plugin.xml$");

	if (count($files)) {
		$get_infos = charger_fonction('get_infos','plugins');
		foreach($files as $k=>$file){
			$files[$k] = substr(dirname($file),strlen($dir));
		}

		$t = $get_infos($files,$force,$dir);
		$themes = array();

		foreach($t as $d=>$info){
			if ($info['categorie']=='theme'
			  AND ($tous OR $info['etat']=='stable')){
				$info['tri'] = strtolower($dir);
				$themes[substr($dir.$d,strlen(_DIR_RACINE))] = $info;
			}
			unset($t[$d]);
		}
	}

	return $themes;
}

?>