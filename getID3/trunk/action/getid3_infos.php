<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
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
	include_spip('inc/documents');
	$fichier = sql_fetsel("*", "spip_documents","id_document=".intval($id_document));
	$file = get_spip_doc($fichier['fichier']);
	if(!file_exists($file))
		return false;
	
	$recuperer_infos = charger_fonction('audio','metadata');
	$infos = $recuperer_infos($file,false);
	
	if($document['titre'] != ''){
		unset($infos['titre']);
	}
	if($document['descriptif'] != ''){
		unset($infos['descriptif']);
	}
	if($document['credits'] != ''){
		unset($infos['credits']);
	}
	if(is_array($infos) && count($infos)>0){
		include_spip('action/editer_document');
		document_modifier($id_document,$infos);
	}
	return $infos;
}

?>