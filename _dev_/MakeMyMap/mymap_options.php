<?php
/*
 * Spip mymap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */


include_spip("base/mymap.php");
//mymap_upgrade("mymap_base_version","0.1.7");

ecrire_meta('mymap_chemin', str_replace("../","",_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

function firstline($str){
	$lines = explode("\n", $str);
	return $lines[0];
}

?>