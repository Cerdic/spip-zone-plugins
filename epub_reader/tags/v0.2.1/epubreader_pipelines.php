<?php
/**
 * ePUB reader
 * Lecteur de fichiers ePUB
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2011-2012 - Distribué sous licence GNU/GPL
 *
 * Fichier de pipelines du plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * Ajoute les scripts js de monocle dans le head des pages
 * 
 * @return $flux Le contexte de pipeline complété
 * @param array $flux Le contexte du pipeline
 */
function epubreader_jquery_plugins($plugins){
	$plugins[] = find_in_path('scripts/monocore.js');
	return $plugins;
}

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * Ajoute la css de monocle dans le head de chaque page 
 * 
 * @return $flux Le contexte de pipeline complété
 * @param array $flux Le contexte du pipeline
 */
function epubreader_insert_head_css($flux){
	$flux .= '
	<link rel="stylesheet" href="'.direction_css(find_in_path('styles/monocore.css')).'" type="text/css" media="all" />';
	return $flux;	
}

/**
 * Insertion dans le pipeline post-edition (SPIP)
 *
 * Intervient à chaque modification d'un objet de SPIP
 * notamment lors de l'ajout d'un document
 *
 * @return $flux Le contexte de pipeline complété
 * @param array $flux Le contexte du pipeline
 */
function epubreader_post_edition($flux){
	$id_document = $flux['args']['id_objet'];
	$infos_doc = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
	
	$mode = $infos_doc['mode'];
	$fichier = $infos_doc['fichier'];
	$extension = $infos_doc['extension'];
		
	/**
	 * A l'ajout du document, on le décompresse dans un répertoire de cache
	 */
	if(in_array($flux['args']['operation'], array('ajouter_document','document_copier_local'))){
		/**
		 * On ne récupère les infos que d'un fichier local de type epub
		 */
		if(($infos_doc['extension'] == 'epub') && ($mode != 'vignette') && ($infos_doc['distant'] == 'non')){
			spip_log('Analyse du fichier '.$fichier,'epub_reader');
			include_spip('epubreader_fonctions');
			$metadonnees = epubreader_recuperer_metas($id_document);
			spip_log($metadonnees,'epub_reader');
			if(count($metadonnees) > 0){
				include_spip('inc/modifier');
				$invalider = true;
				revision_document($id_document, $metadonnees);
				if(isset($metadonnees['cover']) && file_exists($metadonnees['cover'])){
					$mode = 'vignette';
					$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
					$x = $ajouter_documents($metadonnees['cover'], $metadonnees['cover'],
							    $type, $id, $mode, $id_document, $actifs);
				}
			}
			/**
			 * On invalide le cache de cet élément si nécessaire
			 */
			if($invalider){
				include_spip('inc/invalideur');
				suivre_invalideur("1");
			}
		}
	}
	/**
	 * A la suppression du document, on supprime son répertoire de cache s'il existe
	 */
	else if(in_array($flux['args']['operation'],array('supprimer_documents','supprimer_document'))){
		$rep_dest = _DIR_RACINE._DIR_VAR.'cache-epub/'.$id_document;
		if(is_dir($rep_dest)){
			include_spip('inc/invalideur');
			purger_repertoire($rep_dest,array('subdir'=>true));
			spip_unlink($rep_dest);	
		}
	}
	return $flux;
}
?>