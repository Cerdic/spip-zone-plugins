<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3.0
 * Licence GNU/GPL
 * 2010-2012
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function simplecal_liste_themes($select_name, $choix){
	// Version Php5
	//$dir_theme = _DIR_PLUGIN_SIMPLECAL.'css/datepicker/';
	//$dirs = scandir($dir_theme, 0);
	//$dirs = array_slice ($dirs, 2); 

	// Version Php4
	$dir_theme = _DIR_PLUGIN_SIMPLECAL.'css/datepicker/';
	$dh  = opendir($dir_theme);
	while (false !== ($filename = readdir($dh))) {
		$dirs[] = str_replace(".css", "", $filename);
	}
	sort($dirs);
	$dirs = array_slice($dirs, 2); // retire les 2 premiers dossiers (. et ..)

	// -----
	$s="";
	$s.="\n<select name=\"$select_name\">";
	
	foreach ($dirs as $dir){
		if ($dir == $choix){
			$s.="\n\t<option name=\"$dir\" selected=\"selected\">$dir</option>";
		} else {
			$s.="\n\t<option name=\"$dir\">$dir</option>";
		}
	}   
	
	$s.="\n</select>";
	
	return $s;
}
?>