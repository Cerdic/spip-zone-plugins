<?php

// * fraisdon, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;


if (!defined('_DIR_PLUGIN_FRAISDON')){ // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FRAISDON',(_DIR_PLUGINS.end($p)));
}

function fraisdon_insert_head($flux) {
	$flux .= "<!-- DEBUT FRAISDON -->\n";
	$flux .= "<link rel=\"stylesheet\" href=\""._DIR_PLUGIN_FRAISDON."styles/fraisdon.css\" type=\"text/css\" media=\"projection, print, screen, tv\" />\n";
	$flux .= "<script src=\""._DIR_PLUGIN_FRAISDON."js/fraisdon.js\"  type=\"text/javascript\"></script>\n";
	$flux .= "<!-- FIN FRAISDON AFFICHAGE -->\n";
	return $flux;
}




?>
