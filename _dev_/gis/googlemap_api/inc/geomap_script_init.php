<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

function inc_geomap_script_init_dist(){
	static $deja_insere = false;
	if ($deja_insere) return "";
	return '<script type="text/javascript" src="'.generer_url_public('geomap.js').'"></script>';
}

?>