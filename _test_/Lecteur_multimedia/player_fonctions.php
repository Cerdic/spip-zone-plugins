<?php

if (!defined('_DIR_PLUGIN_PLAYER')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PLAYER',(_DIR_PLUGINS.end($p)."/"));
}

function Player_insert_head($flux){
	$flux .= '<script type="text/javascript">var musicplayerurl="'._DIR_PLUGIN_PLAYER.'eraplayer_playlist.swf"</script>';
	//par malchance, firefox2 fait peter jQuery si on le combine avec aflax, on revoit donc la copie.
	//$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'AFLAX/aflax.js"></script>';
	//$flux .= '<script type="text/javascript">var aflax = new AFLAX("'._DIR_PLUGIN_PLAYER.'AFLAX/aflax.swf");</script>';
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'soundmanager/soundmanager2.js"></script>';
	$flux .= '<script type="text/javascript"> 
	soundManager.url = "'._DIR_PLUGIN_PLAYER.'soundmanager/soundmanager2.swf";
	soundManager.consoleOnly = true;
  	soundManager.debugMode = false; 
	</script>';

	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_PLAYER.'player_enclosure.js"></script>';
	$flux .= '<script type="text/javascript">
	<!-- 
	$(document).ready(function(){
	soundManager.onload = function() {  
  // soundManager is initialised, ready to use. Create a sound for this demo page.
 // soundManager.createSound("aDrumSound", "'._DIR_PLUGIN_PLAYER.'soundmanager/mpc/audio/SPLASH_1.mp3");
  }
	//Player_init("'._DIR_PLUGIN_PLAYER.'soundmanager/mpc/audio/AMB_SN13.mp3");
	});
	// --></script>';

	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_PLAYER.'player.css" type="text/css" media="projection, screen, tv" />';
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

function joli_titre($titre){
$titre=basename($titre);
$titre=ereg_replace('.mp3','',$titre);
$titre=ereg_replace('^ ','',$titre);
$titre = eregi_replace("_"," ", $titre );
$titre = eregi_replace("'"," ",$titre );

return $titre ;
}

?>