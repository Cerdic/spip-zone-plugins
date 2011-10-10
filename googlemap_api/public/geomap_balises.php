<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz�lez, Berio Molina
 * (c) 2007 - Distribu�do baixo licencia GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_GEOMAP_INIT_dist($p){
	$p->code = "((\$f=charger_fonction('geomap_script_init','inc',true))?\$f():'')";
	$p->interdire_scripts = false; // securite assuree par la fonction
	return $p;
}

?>