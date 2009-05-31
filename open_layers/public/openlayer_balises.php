<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio Gonz‡lez
 * (c) 2007 - distributed by GNU/GPL licence 
 *
 */

function balise_OPENLAYERS_INIT_dist($p){
	$p->code = "((\$f=charger_fonction('openlayer_script_init','inc',true))?\$f():'')";
	$p->interdire_scripts = false; // securite assuree par la fonction
	return $p;
}

?>