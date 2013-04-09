<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');

function action_getid3_infos_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!intval($arg)){
		spip_log("action_getid3_infos_dist incompris: " . $arg);
		return;
	}
	else{
		action_getid3_infos_post($arg);
		if(_request('redirect')){
			$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
			$GLOBALS['redirect'] = $redirect;
		}
	}
}

function action_getid3_infos_post($id_document){
	$recuperer_infos = charger_fonction('getid3_recuperer_infos','inc');
	$infos = $recuperer_infos($id_document);
	return $infos;
}

?>