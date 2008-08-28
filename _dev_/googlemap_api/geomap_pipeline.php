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
	
if (!defined("_ECRIRE_INC_VERSION")) return;

function geomap_I2_cfg_form($flux) {
	$flux .= recuperer_fond('fonds/inscription2_geoloc');
	return ($flux);
}

?>
