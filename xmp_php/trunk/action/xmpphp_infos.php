<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('inc/actions');

function action_xmpphp_infos_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,", $arg, $r)){
		spip_log("action_infos_video_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}
	else{
		action_xmpphp_infos_post($r);
	}
}

function action_xmpphp_infos_post($r){
	list(, $sign, $id_objet, $objet, $id_document, $suite) = $r;

	$recuperer_infos = charger_fonction('xmpphp_infos','inc');
	$infos = $recuperer_infos($id_document);
	if(_request("iframe") == 'iframe') {
		$redirect = parametre_url(urldecode($iframe_redirect),"show_doc_infos",join(',',$documents_actifs),'&')."&iframe=iframe";
		spip_log($redirect,'xmp');
	}else{
		$redirect = urldecode(_request('redirect'));
		spip_log($redirect,'xmp');
	}
	return $redirect;
}

?>