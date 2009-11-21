<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function zengarden_charge_themes($dir = _DIR_THEMES, $tous = false){
	$themes = array();

	$files = array();
	$files = preg_files($dir,"/plugin.xml$");

	$get_infos = charger_fonction('get_infos','plugins');
	if (count($files))
		foreach($files as $file){
			$path = substr(dirname($file),strlen($dir));
			$infos = $get_infos($path,false,$dir);
			if ($infos
			  AND ($tous OR $infos['etat']=='stable')){
				$infos['chemin'] = $path;
				$themes[$path] = $infos;
			}
		}
	return $themes;	
}

?>