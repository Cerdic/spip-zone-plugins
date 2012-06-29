<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');

function action_spipmotion_logo_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	if (!intval($arg)){
		spip_log("action_logo_video_dist incompris: " . $arg);
	} else {
		$id_logo = action_infos_video_post($arg);
	}
	
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
	}
	return $redirect;
}

function action_infos_video_post($id_document){
	$recuperer_logo = charger_fonction('spipmotion_recuperer_logo','inc');
	$x = $recuperer_logo($id_document);

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_document/$id_document'");
	return $x;
}

?>