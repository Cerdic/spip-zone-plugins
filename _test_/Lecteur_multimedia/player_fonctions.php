<?php

if (!defined('_DIR_PLUGIN_PLAYER')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PLAYER',(_DIR_PLUGINS.end($p)."/"));
}

function Player_insert_head($flux){
	$flux .="<script type='text/javascript'>var musicplayerurl='"._DIR_PLUGIN_PLAYER."musicplayer.swf'</script>\n";
	$flux .= 	'<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'AFLAX/aflax.js"></script>';
	$flux .= 	'<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'player_enclosure.js"></script>';
	$flux .=	'<script type="text/javascript">aflax.insertFlash(1, 1, "#FFFFFF", "go", false);<!--  // --></script>';
	return $flux;
}


/**
 * enclosures
 */

// Contrairement au plugin original (http://zone.spip.org/trac/spip-zone/browser/_plugins_branche_stable_/_spip_1_9_0_/dewplayer)
// Cette version pour la version 1.9.1 utilisera la modification du modèle doc pour traiter les adresses relatives 
// qu'on retrouverait si on placerait un lien dans le texte par une balise <docXX>
// ajout d'un rel="enclosure" simple sur les liens mp3
function Player_post_propre($texte) {

	$reg_formats="mp3";

	//trouver des liens complets 
	$texte = preg_replace(
		",<a(\s[^>]*href=['\"]?(http:\/\/[a-zA-Z0-9\s()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*)>(.*)</a>,Uims",
		'<a$1 rel="enclosure">$4</a>', 
		$texte);
	
	return $texte;
}

?>