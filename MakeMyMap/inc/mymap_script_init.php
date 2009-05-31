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
include_spip('inc/distant');

function inc_mymap_script_init_dist(){
	static $deja_insere = false;
	if ($deja_insere) return "";
	$config = lire_meta('mymap_googlemapkey');
	$mymap = compacte_js(find_in_path('js/mymap.js'));
	$gmap_script = compacte_js(recuperer_page('http://maps.google.com/maps?file=api&v=2&key='.$config));
	return '<script type="text/javascript" src="'.$mymap.'"></script>'
	.'<script type="text/javascript">'.$gmap_script.'</script>';
}

?>