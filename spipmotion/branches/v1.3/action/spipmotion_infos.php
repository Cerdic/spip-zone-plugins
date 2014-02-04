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

function action_spipmotion_infos_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!intval($arg)){
		spip_log("action_spipmotion_infos_dist incompris: " . $arg);
		return;
	}
	else{
		action_spipmotion_infos_post($arg);
		if(_request('redirect')){
			$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
			$GLOBALS['redirect'] = $redirect;
		}
	}
}

function action_spipmotion_infos_post($id_document){
	$recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
	$infos = $recuperer_infos($id_document);
	return $infos;
}

?>