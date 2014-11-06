<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_magnetize_dist(){
	$securiser_action = charger_fonction("securiser_action","inc");
	$arg = $securiser_action();

	list($objet, $id_objet, $action) = explode("-",$arg);

	magnet_set_status($objet, $id_objet, $action=="on"?true:false);
	include_spip("inc/invalideur");
	suivre_invalideur("'id=$objet/$id_objet'");
}

function magnet_set_status($objet, $id_objet, $status){
	$meta_magnet = "magnet_" . table_objet($objet);
	$magnets = (isset($GLOBALS['meta'][$meta_magnet])?$GLOBALS['meta'][$meta_magnet]:'0');
	$magnets = explode(',',$magnets);

	if ($status){
		if (!in_array($id_objet, $magnets)){
			array_unshift($magnets,$id_objet);
		}
	}
	else {
		$magnets = array_diff($magnets,array($id_objet));
	}
	$magnets = array_filter($magnets);
	$magnets = array_unique($magnets);
	if (!count($magnets))
		$magnets[] = "0";
	$magnets = implode(",",$magnets);

	ecrire_meta($meta_magnet,$magnets);
}