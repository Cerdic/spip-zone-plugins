<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio González (c) 2007
 * Distributed under GNU/GPL licence
 *
 */

function openlayer_insert_head_prive($flux){
  $flux .= '<script type="text/javascript" src="'.generer_url_public('openlayer.js').'"></script>';
	return $flux;
}

function openlayer_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.generer_url_public('openlayer.js').'"></script>';
	return $flux;
}

?>