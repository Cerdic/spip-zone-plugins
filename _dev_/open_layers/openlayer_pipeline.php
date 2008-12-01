<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio González
 * (c) 2007 - Distributed under GNU/GPL licence
 *
 */

function openlayer_insert_head_prive($flux){
	$flux .= '<script type="application/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
	<script type="application/javascript" src="'._DIR_PLUGIN_OPENLAYER.'js/openlayers.js"></script>
	<script type="application/javascript" src="http://openstreetmap.org/openlayers/OpenStreetMap.js"></script>';
	return $flux;
}

function openlayer_insert_head($flux){
	$flux .= '<script type="application/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
	<script type="application/javascript" src="'._DIR_PLUGIN_OPENLAYER.'js/openlayers.js"></script>
	<script type="application/javascript" src="http://openstreetmap.org/openlayers/OpenStreetMap.js"></script>';
	return $flux;
}

?>