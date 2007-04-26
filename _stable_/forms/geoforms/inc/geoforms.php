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

function geoforms_latitude_longitude($x,$y,$systeme){
	if (strlen($systeme)){
		include_spip('inc/geoforms_projections');
		list($x,$y) = geoforms_systeme_vers_lat_lont($x,$y,$systeme);
	}
	return array($x,$y);
}

?>