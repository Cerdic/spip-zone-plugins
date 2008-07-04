<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2007-2008, distribue sous licence GNU/GPL
 * 
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// Compatibilite 1.9.2
if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
	include_spip('inc/compat_cfg');
	
// inclure les fonctions lire_config(), ecrire_config() et effacer_config()
include_spip('inc/cfg_config');

// _dir_lib possiblement utile
if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');

// librairies que cfg peut telecharger (SPIP >= 1.9.3)
// via la page ?exec=cfg_install_libs
// en globals pour pouvoir etre etendu par d'autres plugins
//
// ces librairies doivent etre fournis en zip
$GLOBALS['cfg_libs'] = array(
	// farbtastic (color picker)
	'farbtastic' => array(
		'nom' => _T('cfg:lib_farbtastic'), // nom
		'description' => _T('cfg:lib_farbtastic_description'), // description
		'dir' => 'farbtastic12/farbtastic', // repertoire une fois decompresse ou se trouvent les js
		'url' => 'http://acko.net/dev/farbtastic', // url de la documentation
		'install' => 'http://acko.net/files/farbtastic_/farbtastic12.zip' // adresse du zip a telecharger
	)
);


// fonction pour effacer les parametres cfg lors le l'inclusion d'un fond
// utile pour les #FORMULAIRE comme formulaires/cfg.html
// [(#INCLURE{fond=fonds/cfg_toto}{env}|effacer_parametres_cfg)]
function effacer_parametres_cfg($texte){
	return preg_replace('/(<!-- ([a-z0-9_]\w+)(\*)?=)(.*?)-->/sim', '', $texte);		
}

// signaler le pipeline de notification
$GLOBALS['spip_pipeline']['cfg_post_edition'] = "";


?>
