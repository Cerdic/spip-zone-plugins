<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2011 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');

function action_spipmotion_logo_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	if (!preg_match(",^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,", $arg, $r)){
		spip_log("action_logo_video_dist incompris: " . $arg);
	} else {
		$id_logo = action_infos_video_post($r);
	}
	
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		//redirige_par_entete($redirect);
	}
	return $redirect;
}

function action_infos_video_post($r){
	list(, $sign, $id, $type, $id_document, $suite) = $r;
	$recuperer_logo = charger_fonction('spipmotion_recuperer_logo','inc');
	$x = $recuperer_logo($id_document);

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_document/$id_document'");
	spip_log($x,'spipmotion');
	return $x;
}

?>