<?php

/**
 * Action doc2img_convert.php
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');

/**
 * Action nécessaire à la conversion d'un document
 *
 * Traite juste l'action :
 * - Récupère l'id_document
 * - Retourne vers la page demandée ou à defaut la page appelante
 *
 * @param $redirect url de redirection (obtenue via _request())
 * @param $id_document id_document fourni par le contexte (via _request())
 */
function action_doc2img_convert_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$arg = explode('-',$arg);
	list($id_document, $action) = $arg;
    //on lance la conversion du document
    if ($id_document = intval($id_document)) {
    	$convertir = charger_fonction('doc2img_convertir','inc');
		spip_log('conversion du doc '.$id_document,'doc2img');
		if(defined('_DIR_PLUGIN_FACD')){
			include_spip('action/facd_ajouter_conversion');
			facd_ajouter_conversion_file($id_document,'doc2img_convertir',null,$action,'doc2img');
			$conversion_directe = charger_fonction('facd_convertir_direct','inc');
			$conversion_directe();
		}else{
    		$convertir($id_document,$action);
    	}
    	include_spip('inc/invalideur');
    	suivre_invalideur("id='id_document/$id_document'");
    }

    if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
	}
	return $redirect;
}
?>
