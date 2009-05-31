<?php
/**
 * Plugin Kaltura
 * (c) 2008 Cedric MORIN, www.yterium.com
 *
 */

/**
 * Supprimer un kaltura
 *
 */
function action_supprimerkaltura_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_document = $securiser_action();
	if ($id_document=intval($id_document)){
		include_spip('base/abstract_sql');
		if ($url = sql_getfetsel('fichier','spip_documents','id_document='.intval($id_document))){
			include_spip('inc/kaltura');
			$kshow_id = kaltura_kshow_id_from_url($url);

			include_spip('inc/invalideur');
			suivre_invalideur("document/$id_document");
			
			// supprimer tous les liens
			include_spip('inc/documents');
			supprimer_documents(array($id_document));
			kaltura_delete(array('kshow_id'=>$kshow_id));
		}
	}
}

?>