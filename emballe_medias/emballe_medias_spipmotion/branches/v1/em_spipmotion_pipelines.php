<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Plugin Emballe Medias SPIPMotion
 *
 * Auteurs :
 * Quentin Drouet (kent1@arscenic.info)
 *
 * © 2009/2010 - Distribue sous licence GNU/GPL
 *
 * Insertion dans les pipelines
 **/

/**
 * Insertion dans le pipeline post_edition
 * Supprime les versions encodées créées par spipmotion s'il y a lieu
 *
 * @param array $flux L'environnement fourni par le pipeline
 * @return array $flux L'environnement complété
 */
function em_spipmotion_post_edition($flux){
	if($flux['args']['operation'] == 'supprimer_document'){
		include_spip('action/spipmotion_ajouter_file_encodage');
		spipmotion_supprimer_versions($flux['args']['id_objet']);
	}
	return $flux;
}

/**
 * Insertion de contenu au début du formulaire
 *
 * Permet notamment d'insérer du contenu javascript ou textuel non prévu
 * par le plugin central
 *
 * @param array $flux Le contexte
 * @return array $flux le contexte complété
 */
function em_spipmotion_diogene_avant_formulaire($flux){
	if($flux['args']['type'] == 'article'){
    	$flux['data'] .= recuperer_fond('prive/em_spipmotion_avant_formulaire', $flux['args']);
	}
    return $flux;
}

/**
 * Insertion dans le pipeline em_post_upload_medias
 * Dans le cas d'un réimport de document, on supprime ses encodages qui ont de fortes chances de ne plus fonctionner
 * 
 * @param array $flux Le contexte
 * @return array $flux le contexte complété
 */
function em_spipmotion_em_post_upload_medias($flux){
	if(is_numeric($flux['args']['ancien_document']) && ($flux['args']['action_document'] == 'remplacer_document')){
		include_spip('inc/documents');
		$docs_convertis = sql_select('*','spip_documents','id_orig='.intval($flux['args']['id_document']));
		while($doc = sql_fetch($docs_convertis)){
			if(!file_exists(get_spip_doc($doc['fichier']))){
				sql_delete('spip_documents','id_document='.intval($doc['id_document']));
			}
		}
	}
	return $flux;
}
?>
