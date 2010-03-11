<?php

// inc/foni_api.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/**********************************************
 * Copyright (c) 2010 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 **********************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

function foni_autoriser_modifier () {
	
	return(
		   ($GLOBALS['connect_statut'] == '0minirezo')
		   && ($GLOBALS['connect_toutes_rubriques'])
		   );
}

/*
 * Liste les fontes dispo en scrutant dans les divers dossiers
 * de squelettes.
 * @return array
 * 	(
		'fontname' => "filename.eot|filename.ttf|font_directory"
		, ...
	)
 * */
function foni_fonts_collecter () {
	
	$available_fonts = array();

	// Lister les fontes...
	// d'abord les fontes fournies avec le plugin
	$skel_dirs = array(trim(_DIR_PLUGIN_FONI, './'));
	
	// celle du ou des squelettes perso
	if(isset($GLOBALS['dossier_squelettes']))
	{
		$skel_dirs = array_merge($skel_dirs, explode(':', $GLOBALS['dossier_squelettes']));
	}
	
	// dans le squelette, a' la racine, dans la dist
	$skel_dirs = array_merge($skel_dirs, array('squelettes', '.', 'squelettes-dist'));
							 
	$ii = (test_espace_prive() ? '../' : '');
	foreach($skel_dirs as $dir)
	{
		$available_fonts = foni_fontes_lister(rtrim($ii.$dir, '/') . '/polices', $available_fonts);
	}
	
	return($available_fonts);
}

/*
 * Lire le répertoire proposé
 * En liste les fontes (toujours par deux : eot et ttf)
 * @return array 
 * 	(
		'fontname' => "filename.eot|filename.ttf|font_directory"
		, ...
	)
 * */
function foni_fontes_lister ($dir, $fonts_list) {
	
	$exts = array('ttf', 'eot');
	
	$font_files = array();
	
	foreach($exts as $ii) {
		if(!isset($fonts_list[$ii])) {
			$font_files[$ii] = array();
		}
	}
	foni_log('cherche:dir:'.$dir);
	if(is_dir($dir))
	{
		//foni_log('listing fonts from '.$dir);
		if($dh = opendir($dir))
		{
			while(($file = readdir($dh)) !== false) {
				$path_parts = pathinfo($file);
				if(in_array($ii = strtolower($path_parts['extension']), $exts)) {
					// rajouter à la pile des fontes
					$font_files[$ii][$path_parts['filename']] = basename($file);
				}
			}
			closedir($dh);

			// se débarasser des . relatifs
			$dir = ltrim($dir, './');
			
			// pour chaque eot, chercher son ttf
			if(count($font_files['eot'])) {
				foreach($font_files['eot'] as $ii => $val) {
					if(isset($font_files['ttf'][$ii])) {
						$fonts_list[$ii] = $font_files['eot'][$ii] . _FONI_SEPARATOR . $font_files['ttf'][$ii] . _FONI_SEPARATOR . $dir;
						foni_log($dir.'/'.$ii);
					}
				}
			}
		}
		else
		{
			spip_log('Fontes Importer: ' . $dir . ' unreadable directory');
		}
	}
	return($fonts_list);
}
