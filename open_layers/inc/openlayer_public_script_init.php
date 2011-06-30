<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio Gonz�lez (c) 2007
 *
 * Distributed under GNU/GPL licence
 *
 */
 
include_spip('inc/distant');

function inc_openlayer_public_script_init_dist(){
	$out = '<script type="application/javascript" src="'.generer_url_public('openlayer.js').'"></script>';
	return $out;
}
?>