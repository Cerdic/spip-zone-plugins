<?php
/**
 * Action doc2img_convert.php
 * 
 * Ajoute un fichier dans la liste d'attente de conversion de FACD
 * 
 * @package SPIP\Doc2img\Actions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');

/**
 * Action nécessaire à la conversion d'un document
 *
 * Traite juste l'action :
 * - Récupère l'id_document
 * - Retourne vers la page demandée ou à defaut la page appelante
 *
 * @param string $redirect 
 * 		URL de redirection (obtenue via _request())
 * @param int $id_document 
 * 		id_document fourni par le contexte (via _request())
 */
function action_doc2img_convert_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$arg = explode('-',$arg);
	list($id_document, $action) = $arg;

    if ($id_document = intval($id_document)){
		include_spip('action/facd_ajouter_conversion');
		facd_ajouter_conversion_file($id_document,'doc2img_convertir',null,$action,'doc2img');
		$conversion_directe = charger_fonction('facd_convertir_direct','inc');
		$conversion_directe();
    	include_spip('inc/invalideur');
    	suivre_invalideur("id='id_document/$id_document'");
    }

    if(_request('redirect')){
		$GLOBALS['redirect'] = str_replace('&amp;','&',urldecode(_request('redirect')));
	}
}
?>
