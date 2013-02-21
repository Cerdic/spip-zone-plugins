<?php
/**
 * Plugin Doc2img
 * Fichier contenant les appels aux pipelines de SPIP
 * 
 * @package SPIP\Doc2img\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline post_edition (SPIP)
 * Ajoute automatiquement dans la file d'attente de conversion les fichiers à transformer
 *
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux 
 * 		Le contexte du pipeline modifié
 */
function doc2img_post_edition($flux) {
    $id_document = $flux['args']['id_objet'];

    if (in_array($flux['args']['operation'], array('ajouter_document','document_copier_local'))
            && (sql_countsel("spip_documents as L1 LEFT JOIN spip_documents_liens as L2 ON L1.id_document=L2.id_document","L2.id_objet=".intval($flux['args']['id_objet']).' AND L2.objet="document" AND L1.mode="doc2img"') == 0)
            && (lire_config('doc2img/conversion_auto') == "on")){
            	$infos_doc = sql_fetsel('extension,mode,fichier,mode,distant','spip_documents','id_document='.intval($id_document));
            	$types_autorises = explode(',',lire_config("doc2img/format_document",null,true));
				if($infos_doc['extension'] == 'tif'){
					$infos_doc['extension'] = 'tiff';
				}
            	if(($infos_doc['mode'] != 'vignette')
            		&& ($infos_doc['distant'] == 'non')
            		&& in_array($infos_doc['extension'],$types_autorises)){
        				include_spip('action/facd_ajouter_conversion');
						facd_ajouter_conversion_file($id_document,'doc2img_convertir',null,null,'doc2img');
						$conversion_directe = charger_fonction('facd_convertir_direct','inc');
						$conversion_directe();
            	}
    }
	if($flux['args']['operation'] == 'supprimer_document'){
		$v = sql_select("*","spip_documents as L1 LEFT JOIN spip_documents_liens as L2 ON L1.id_document=L2.id_document","L2.id_objet=".intval($flux['args']['id_objet']).' AND L2.objet="document" AND L1.mode="doc2img"');
		include_spip('action/dissocier_document');
		while($conversion = sql_fetch($v)){
			supprimer_lien_document($conversion['id_document'], 'document', $flux['args']['id_objet'],true);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * Vérifie au chargement du formulaire de configuration que l'on a bien accès à la class Imagick 
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié : editable à false et un message d'erreur si pas de class Imagick
 */
function doc2img_formulaire_charger($flux) {
	if($flux['args']['form'] == 'configurer_doc2img'){
		if (!class_exists('Imagick')) {
			$flux['editable'] = false;
			$flux['message_erreur'] = _T('doc2img:erreur_class_imagick');	
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * Vérifie la configuration du formulaire de configuration 
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline auquel on a ajouté nos erreurs
 */
function doc2img_formulaire_verifier($flux) {
	if($flux['args']['form'] == 'configurer_doc2img'){
		include_spip('inc/config');
		if(!is_array($formats = lire_config('doc2img_imagick_extensions'))){
			include_spip('inc/metas');
			$imagick = new Imagick();
			$formats = $imagick->queryFormats();
			ecrire_meta('doc2img_imagick_extensions',serialize($formats));
		}
		if(_request('format_document')){
			$formats_choisis = explode(',',trim(_request('format_document')));
			$diff = array_diff(array_map('trim',array_map('strtolower',$formats_choisis)),array_map('trim',array_map('strtolower',$formats)));
			$formats = array_map('trim',array_map('strtolower',explode(',',trim(_request('format_document')))));
			set_request('format_document',implode(',',$formats));
		}
		if(count($diff) > 1){
			$flux['format_document'] = _T('doc2img:erreur_formats_documents',array('types'=>implode(',',$diff)));
		}else if(count($diff) == 1){
			$flux['format_document'] = _T('doc2img:erreur_format_document',array('type'=>implode(',',$diff)));
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * On affiche en dessous des documents les pages converties
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline auquel on a ajoute le html dans data
 */
function doc2img_recuperer_fond($flux){
	if ($flux['args']['fond']=='modeles/document_desc'){
		$flux['data']['texte'] .= recuperer_fond('prive/inclure/document_desc_liste_doc2img',$flux['args']['contexte']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline medias_documents_visibles (Plugin medias)
 * 
 * On ajoute le fait que les documents ayant comme mode doc2img soient visibles et non pas
 * supprimés des boucles documents
 *
 * @param array $flux
 * 		Le contexte du pipeline, tableau des types de documents visibles possibles
 * @return array $flux
 * 		Le contexte du pipeline modifié, tableau auquel on a ajouté doc2img
 */
function doc2img_medias_documents_visibles($flux){
	$flux[] = 'doc2img';
	return $flux;
}

/**
 * Insertion dans le pipeline document_desc_actions (Plugin Médias)
 * Ajouter le bouton de conversion de document dans le bloc de document 
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié avec le bouton de conversion 
 */
function doc2img_document_desc_actions($flux) {
	$id_document = $flux['args']['id_document'];
	$infos = sql_fetsel('*', 'spip_documents', 'id_document=' . intval($id_document));
	$types_autorises = explode(',',lire_config("doc2img/format_document",null,true));
	if($infos['extension'] == 'tif'){
		$infos['extension'] = 'tiff';
	}
	if(($infos['mode'] != 'vignette')
		&& ($infos['distant'] == 'non')
		&& in_array($infos['extension'],$types_autorises)){
			$fond='prive/doc2img_media_boutons';
		if ($flux['args']['position'] == 'galerie') {
			$flux['data'] .= recuperer_fond($fond,array('mode'=>'galerie','id_document'=>$id_document));
		} else {
			$flux['data'] .= recuperer_fond($fond,array('id_document'=>$id_document));
		}
	}
	return $flux;
}
?>