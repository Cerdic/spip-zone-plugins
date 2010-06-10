<?php

/**
 * Plugin Doc2img
 * Fichier contenant les appels aux pipelines de SPIP
 */

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

            	if(($infos_doc['mode'] != 'vignette')
            		&& ($infos_doc['distant'] == 'non')
            		&& in_array($infos_doc['extension'],$types_autorises)){
			    		$convertir = charger_fonction('doc2img_convertir','inc');
			    		$convertir($id_document);
            	}
    }
	return $flux;
}

?>