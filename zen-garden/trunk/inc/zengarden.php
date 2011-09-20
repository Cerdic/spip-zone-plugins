<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2011 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!defined('_DIR_THEMES'))
	define('_DIR_THEMES',_DIR_RACINE."themes/");

function zengarden_charge_themes($dir = _DIR_THEMES, $tous = false){
	$themes = array();

	$files = preg_files($dir,"/plugin.xml$");

	if (count($files)) {
		$get_infos = charger_fonction('get_infos','plugins');
		foreach($files as $k=>$file){
			$files[$k] = substr(dirname($file),strlen(_DIR_RACINE));
		}

		$themes = $get_infos($files,false,_DIR_RACINE);

		foreach($themes as $d=>$info){
			if ($info['categorie']!='theme'
			  OR (!$tous AND $info['etat']!=='stable'))
				unset($themes[$d]);
			else
				$themes[$d]['tri'] = strtolower($dir);
		}
	}

	return $themes;
}

?>