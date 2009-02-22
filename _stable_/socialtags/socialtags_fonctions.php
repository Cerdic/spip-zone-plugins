<?php
if (!defined('_DIR_PLUGIN_SOCIALTAGS')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SOCIALTAGS',(_DIR_PLUGINS.end($p)."/"));
}
 
//
// ajout feuille de stylle
//
function socialtags_insert_head($flux){
  $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('socialtags.css').'" media="projection, screen, tv" />';

  if (lire_config('socialtags/jshead')) {
  	$jsFile = generer_url_public('socialtags.js');
  	$flux .= "<script src='$jsFile' type='text/javascript'></script>";
  }

  return $flux;
}


?>
