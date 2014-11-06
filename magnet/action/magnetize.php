<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Modifier l'etat magnetise
 */
function action_magnetize_dist(){
	$securiser_action = charger_fonction("securiser_action","inc");
	$arg = $securiser_action();

	list($objet, $id_objet, $action) = explode("-",$arg);

	if (in_array($action,array('on','off'))){
		magnet_set_status($objet, $id_objet, $action=="on"?true:false);
	}
	if (in_array($action,array('up','down'))){
		magnet_set_order($objet, $id_objet, $action=="up"?-1:+1);
	}

	include_spip("inc/invalideur");
	suivre_invalideur("'id=$objet/$id_objet'");
}

/**
 * Modifier l'ordre
 * @param string $objet
 * @param int $id_objet
 * @param int $offset
 */
function magnet_set_order($objet, $id_objet, $offset){
	$meta_magnet = "magnet_" . table_objet($objet);
	$magnets = (isset($GLOBALS['meta'][$meta_magnet])?$GLOBALS['meta'][$meta_magnet]:'0');
	$magnets = explode(',',$magnets);
	if (!in_array($id_objet,$magnets))
		return;

	$index = array_search($id_objet,$magnets);

	$newindex = $index + $offset;
	$newindex = min($newindex,count($magnets)-1);
	$newindex = max($newindex,0);

	// on le retire
	$magnets = array_diff($magnets, array($id_objet));
	$newmagnets = array_slice($magnets,0,$newindex);
	$newmagnets[] = $id_objet;
	$newmagnets = array_merge($newmagnets,array_slice($magnets,$newindex));

	ecrire_meta($meta_magnet,implode(",",$newmagnets));
}

/**
 * Activer/desactiver la magnetisation
 * @param string $objet
 * @param int $id_objet
 * @param bool $status
 */
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

	if (count($magnets)>20)
		$magnets = array_slice($magnets,0,20);

	$magnets = implode(",",$magnets);

	ecrire_meta($meta_magnet,$magnets);
}