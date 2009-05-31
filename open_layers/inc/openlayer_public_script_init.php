<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio Gonz‡lez
 * (c) 2007 - Distributed under GNU/GPL licence
 *
 */
 
include_spip('inc/distant');

function inc_openlayer_public_script_init_dist(){
	$out = '<script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
	<script type="text/javascript" src="'._DIR_PLUGIN_OPENLAYER.'js/openlayers.js"></script>
	<script type="text/javascript" src="http://openstreetmap.org/openlayers/OpenStreetMap.js"></script>';
	return $out;
}
?>