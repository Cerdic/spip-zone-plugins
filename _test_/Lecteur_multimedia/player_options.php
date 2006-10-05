<?php

/**
 * definir le player par defaut.
 */

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_LECTEUR_MULTIMEDIA',(_DIR_PLUGINS.end($p)));
 

//la valeur se change dans ?exec=player_admin

  if (!isset($GLOBALS['meta']['player'])) {
 	           $player = "neoplayer" ;
 	           ecrire_meta('player', $player);
 	           ecrire_metas();
 }              	  

?>