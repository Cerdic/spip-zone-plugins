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


function geoforms_ajouter_boutons($boutons_admin) {
	if (autoriser('administrer','geoforms')) {
	    $boutons_admin['configuration']->sousmenu['geoforms_config']= new Bouton(
		    _DIR_PLUGIN_GEOFORMS.'img_pack/geoforms.png', _T('geoforms:configuration'));
	}
	return $boutons_admin;
}

?>