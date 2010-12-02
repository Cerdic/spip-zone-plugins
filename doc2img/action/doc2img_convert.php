<?php

/**
 * Action doc2img_convert.php
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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

    //on lance la conversion du document
    if ($id_document = intval($arg)) {
    	$convertir = charger_fonction('doc2img_convertir','inc');
    	$convertir($id_document);
    	include_spip('inc/invalideur');
    	suivre_invalideur("id='id_document/$id_document'");
    }

    $redirect = _request('redirect');
    //charge la page donnée par $redirect à defaut la page appelante
	if (!$redirect){
        $redirect = $_SERVER['HTTP_REFERER'];
	}else {
		$redirect = urldecode($redirect);
	}

	include_spip('inc/headers');
    redirige_par_entete($redirect);
}
?>
