<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('inc/actions');

function action_spipmotion_infos_dist(){
	global $redirect;
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,", $arg, $r)){
		spip_log("action_infos_video_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}
	else{
		action_spipmotion_infos_post($r);
		spip_log("action spipmotion_infos","spipmotion");
	}
}

function action_spipmotion_infos_post($r){
	list(, $sign, $id_objet, $objet, $id_document, $suite) = $r;
	
	$recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
	$infos = $recuperer_infos($id_document);
	
	if(_request("iframe") == 'iframe') {
		$redirect = parametre_url(urldecode($iframe_redirect),"show_video_infos",join(',',$documents_actifs),'&')."&iframe=iframe";
	}
	return $redirect;
}

?>