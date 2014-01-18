<?php
//
// ajout bouton
// 
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_PB_COULEUR_ARTICLE',(_DIR_PLUGINS.end($p)));

if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
define('_DIR_FARBTASTIC_LIB', _DIR_LIB . 'farbtastic-1/'); 
 


 
//
// functions
//

function pb_couleur_article($id_article) {
			$pb_couleur_article = lire_meta("pb_couleur_article$id_article");
//			if (!$pb_couleur_article) $pb_couleur_article = "#999999";
	
	return $pb_couleur_article;
}

function couleur_article($id_article) {
	return pb_couleur_article($id_article);
}

?>
