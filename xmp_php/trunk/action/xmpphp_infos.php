<?php
/**
 * XMP php
 * Récupération des métadonnées XMP
 *
 * Auteur : kent1 (kent1@arscenic.info - http://www.kent1.info)
 * ©2011-2013 - Distribué sous licence GNU/GPL
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_xmpphp_infos_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!intval($arg)){
		spip_log("action_xmpphp_infos_dist incompris: " . $arg);
		return;
	}
	else{
		action_xmpphp_infos_post($arg);
		if(_request('redirect')){
			$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
			$GLOBALS['redirect'] = $redirect;
		}
	}
}

function action_xmpphp_infos_post($arg){
	$recuperer_infos = charger_fonction('xmpphp_infos','inc');
	$infos = $recuperer_infos($arg);
	return $infos;
}

?>