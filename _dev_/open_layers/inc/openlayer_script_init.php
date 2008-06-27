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

function inc_openlayer_script_init_dist(){
	static $deja_insere = false;
	if ($deja_insere) return "";
	$out = '<script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>';
	return $out;
}
?>