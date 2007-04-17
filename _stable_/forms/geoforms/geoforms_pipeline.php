<?php
/*
 * GeoForms
 * Geolocalistion dans les tables et les formulaires
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function geoforms_header_prive($flux){
	$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_GEOFORMS."geoforms.css' type='text/css' media='all' />\n";
	return $flux;
}

?>