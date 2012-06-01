<?php
//
// ajout bouton
// 
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_PB_COULEUR_RUBRIQUE',(_DIR_PLUGINS.end($p)));

if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
define('_DIR_FARBTASTIC_1_3_LIB', _DIR_LIB . 'farbtastic_1_3_1/'); 
 


 
//
// functions
//

function pb_couleur_rubrique($id_rubrique) {
			$pb_couleur_rubrique = lire_meta("pb_couleur_rubrique$id_rubrique");
//			if (!$pb_couleur_rubrique) $pb_couleur_rubrique = "#999999";
	
	return $pb_couleur_rubrique;
}

function couleur_rubrique($id_rubrique) {
	return pb_couleur_rubrique($id_rubrique);
}

?>
