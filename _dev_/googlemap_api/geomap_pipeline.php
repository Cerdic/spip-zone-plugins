<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio González, Berio Molina
 * (c) 2007 - Distribuído baixo licencia GNU/GPL
 *
 */

function geomap_ajouterBoutons($boutons_admin) {
	// si eres administrador
	if (autoriser('administrer','geomap')) {
    // vese o botón na barra de "configuración"
	    $boutons_admin['configuration']->sousmenu['geomap_config']= new Bouton(
		    _DIR_PLUGIN_GEOMAP.'img_pack/correxir.png', _T('geomap:configuration'));
	}
	return $boutons_admin;
}
?>