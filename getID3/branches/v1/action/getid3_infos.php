<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1), BoOz
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('inc/actions');

function action_getid3_infos_dist(){
	global $redirect;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,", $arg, $r)){
		spip_log("action_getid3_infos_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}
	else{
		action_getid3_infos_post($r);
	}
}

function action_getid3_infos_post($r){
	list($arg, $sign, $id_objet, $objet, $id_document, $suite) = $r;

	$recuperer_infos = charger_fonction('getid3_recuperer_infos','inc');
	$infos = $recuperer_infos($id_document);

	if(_request("iframe") == 'iframe') {
		$redirect = parametre_url(urldecode($iframe_redirect),"show_video_infos",join(',',$documents_actifs),'&')."&iframe=iframe";
	}
	return $redirect;
}

?>