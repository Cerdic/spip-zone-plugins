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

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * 
 * On affiche les informations du document
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function xmpphp_recuperer_fond($flux){
	if ($flux['args']['fond']=='modeles/document_desc'){
		if(isset($flux['args']['contexte']['id_document']) && ($flux['args']['contexte']['id_document'] > 0)){
			$infos_doc = sql_fetsel("distant,extension,mode", "spip_documents","id_document=".intval($flux['args']['contexte']['id_document']));
			if(($infos_doc['distant'] == 'non') && in_array($infos_doc['mode'],array('document','image')) && in_array($infos_doc['extension'],array('ai','eps','jpg','pdf','png','psd','tif','tiff')))
				$flux['data']['texte'] .= recuperer_fond('prive/xmpphp_infos_fichier', $flux['args']['contexte']);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline document_desc_actions (Medias)
 * On ajoute un lien pour récupérer les informations xmp des fichiers
 * 
 * @param array $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline complété
 */
function xmpphp_document_desc_actions($flux){
	$flux['data'] .= recuperer_fond('prive/squelettes/inclure/xmpphp_document_desc_action',$flux['args']);
	return $flux;
}

/**
 * Insertion dans le pipeline media_renseigner_metas (Medias)
 * à partir de SPIP 3.11
 * Récupérer les métas d'un document lors de son insertion en base
 * 
 * @param array $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline complété
 */
function xmpphp_renseigner_document($flux){
	if(isset($flux['args']['extension']) && isset($flux['args']['fichier']) && in_array($flux['args']['extension'],array('ai','eps','jpg','pdf','png','psd','tif','tiff'))){
		$recuperer_infos = charger_fonction('xmpphp_infos','inc');
		$infos = $recuperer_infos(false,$flux['args']['fichier'],true);
		if(is_array($flux['data']) && is_array($infos))
			$flux['data'] = array_merge($infos,$flux['data']);
	}
	return $flux;
}
?>