<?php

/**
 * Plugin Doc2img
 * Fichier contenant les appels aux pipelines de SPIP
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline affiche_gauche
 *
 * @param $flux flux html de la partie gauche
 * @return $flux le flux html completé
 */
function doc2img_affiche_gauche($flux) {
	switch ($flux['args']['exec']) {
		case "articles" :
			$id_article = $flux['args']['id_article'];
			$flux['data'] .= debut_cadre('r');
			$flux['data'] .= recuperer_fond("prive/doc2img",array('id_article'=>$id_article));
			$flux['data'] .= fin_cadre('r');
			break;
	}
	return $flux;
}

/**
 * Insertion dans le pipeline post-edition
 * Converti automatiquement les fichiers autorisés si possible
 *
 * @param $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline modifié
 */
function doc2img_post_edition($flux) {
    $id_document = $flux['args']['id_objet'];

    if (in_array($flux['args']['operation'], array('ajouter_document','document_copier_local'))
            && (sql_countsel('spip_doc2img','id_document='.intval($id_document)) == 0)
            && (lire_config('doc2img/conversion_auto') == "on")){
            	$infos_doc = sql_fetsel('extension,mode,fichier,mode,distant','spip_documents','id_document='.intval($id_document));
            	$types_autorises = explode(',',lire_config("doc2img/format_document",null,true));
				if($infos_doc['extension'] == 'tif'){
					$infos_doc['extension'] = 'tiff';
				}
            	if(($infos_doc['mode'] != 'vignette')
            		&& ($infos_doc['distant'] == 'non')
            		&& in_array($infos_doc['extension'],$types_autorises)){
			    		$convertir = charger_fonction('doc2img_convertir','inc');
			    		$convertir($id_document);
            	}
    }
	if($flux['args']['operation'] == 'supprimer_document'){

		$v = sql_select("id_doc2img,fichier","spip_doc2img","id_document=".intval($flux['args']['id_objet']));

		include_spip('inc/documents');

		while($version = sql_fetch($v)){
			$liste[] = $version['id_doc2img'];
			if (@file_exists($f = get_spip_doc($version['fichier']))) {
				supprimer_fichier($f);
			}
		}
		if(is_array($liste)){
			$in = sql_in('id_doc2img', $liste);
			sql_delete("spip_doc2img", $in);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline document_desc_actions (Plugin Mediathèque)
 * Ajouter le bouton de conversion de document dans le bloc de document 
 *
 * @param array $flux
 * @return array $flux
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

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * Vérifie au chargement du formulaire de configuration que l'on a bien accès à la class Imagick 
 *
 * @param array $flux
 * @return array $flux
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
 * @return array $flux
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
?>