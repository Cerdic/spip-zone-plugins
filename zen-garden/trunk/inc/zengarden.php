<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2011 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function zengarden_charge_themes($dir = _DIR_THEMES, $tous = false){
	$themes = array();

	$files = preg_files($dir,"/plugin.xml$");

	if (count($files)) {
		$get_infos = charger_fonction('get_infos','plugins');
		foreach($files as $k=>$file){
			$files[$k] = substr(dirname($file),strlen($dir));
		}

		$themes = $get_infos($files,false,$dir);

		foreach($themes as $dir=>$info){
			if ($info['categorie']!='theme'
			  OR (!$tous AND $info['etat']!=='stable'))
				unset($themes[$dir]);
			else
				$themes[$dir]['tri'] = strtolower($dir);
		}
	}
	return $themes;
}

?>