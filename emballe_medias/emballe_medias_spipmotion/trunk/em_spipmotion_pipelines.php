<?php
/**
 * Plugin Emballe Medias SPIPMotion
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2009/2013 - Distribue sous licence GNU/GPL
 *
 * Insertion dans les pipelines
 **/

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline diogene_avant_formulaire (Diogene)
 * 
 * Insert du contenu au début du formulaire
 * Permet notamment d'insérer du contenu javascript ou textuel non prévu
 * par le plugin central
 *
 * @param array $flux Le contexte
 * @return array $flux le contexte complété
 */
function em_spipmotion_diogene_avant_formulaire($flux){
	if(!test_espace_prive() && isset($flux['args']['type']) && ($flux['args']['type'] == 'article')){
    	$flux['data'] .= recuperer_fond('prive/em_spipmotion_avant_formulaire', $flux['args']);
	}
    return $flux;
}

/**
 * Insertion dans le pipeline em_post_upload_medias (emballe_medias)
 * Dans le cas d'un réimport de document, on supprime ses encodages qui ont de fortes chances 
 * de ne plus fonctionner
 * 
 * @param array $flux Le contexte
 * @return array $flux le contexte complété
 */
function em_spipmotion_em_post_upload_medias($flux){
	if(isset($flux['args']['ancien_document']) 
		&& is_numeric($flux['args']['ancien_document'])
		&& isset($flux['args']['action_document'])
		&& ($flux['args']['action_document'] == 'remplacer_document')){
		include_spip('inc/documents');
		$docs_convertis = sql_select('doc.id_document',
							'spip_documents AS doc LEFT JOIN spip_documents_liens AS lien',
							'doc.mode="conversion" AND lien.objet="document" AND lien.id_objet='.intval($flux['args']['id_document']));
		while($doc = sql_fetch($docs_convertis)){
			if(!file_exists(get_spip_doc($doc['fichier']))){
				sql_delete('spip_documents','id_document='.intval($doc['id_document']));
			}
		}
	}
	return $flux;
}
?>
