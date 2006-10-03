<?php

/**
 * definir le player par defaut.
 */

//la valeur se change dans ?exec=player_admin

  if (!isset($GLOBALS['meta']['player'])) {
 	           $player = "neoplayer" ;
 	           ecrire_meta('player', $player);
 	           ecrire_metas();
 }              	  

?>