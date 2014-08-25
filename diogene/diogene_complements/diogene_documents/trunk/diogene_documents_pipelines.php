<?php
/**
 * Plugin Diogene Documents
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 
 * © 2014 - Distribue sous licence GNU/GPL
 *
 * Utilisation des pipelines par Diogene Documents
 *
 * @package SPIP\Diogene Documents\Pipelines
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (Diogene)
 *
 * Ajout des saisies supplémentaires dans le formulaire
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_documents_diogene_ajouter_saisies($flux){
	if (is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('documents',unserialize($flux['args']['champs_ajoutes']))){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['contexte'][$id_table_objet];
		if(is_array(unserialize($flux['args']['options_complements']['champs_documents'])))
			$flux['args']['contexte']['champs_documents'] = unserialize($flux['args']['options_complements']['champs_documents']);
		else
			$flux['args']['contexte']['champs_documents'] = array();

		if(intval($id_objet) > 0){
			$documents_objet = sql_allfetsel('*','spip_documents as docs LEFT JOIN spip_documents_liens as liens on docs.id_document = liens.id_document','liens.objet='.sql_quote($objet).' AND liens.id_objet='.intval($id_objet));
			foreach($documents_objet as $doc){
				$id_document = $doc['id_document'];
				foreach(array('titre','credits','descriptif','supprimer') as $champ){
					$champ_ok = $champ.'_'.$id_document;
					$flux['args']['contexte'][$champ_ok] = _request($champ_ok) ? _request($champ_ok) : $doc[$champ];
				}
			}
		}
		$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_documents',$flux['args']['contexte']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_verifier (Diogene)
 * 
 * Vérification des formulaires qui sont modifiés par Diogene
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_documents_diogene_verifier($flux){
	$id_diogene = _request('id_diogene');
	if(intval($id_diogene)){
		$options_complements = unserialize(sql_getfetsel("options_complements","spip_diogenes","id_diogene=".intval($id_diogene)));
		$erreurs = $flux['args']['erreurs'];
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_traiter (Diogene)
 * Fonction s'exécutant au traitement des formulaires modifiés par Diogene
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_documents_diogene_traiter($flux){
	$pipeline = pipeline('diogene_objets');
	if (in_array($flux['args']['type'],array_keys($pipeline)) && isset($pipeline[$flux['args']['type']]['champs_sup']['documents']) AND ($id_diogene = _request('id_diogene'))) {
		$objet = $flux['args']['type'];
		$id_objet = $flux['args']['id_objet'];
		
		$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
		if (is_array($post)){
			include_spip('inc/joindre_document');
			include_spip('formulaires/joindre_document');
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');
			$mode = joindre_determiner_mode('auto','new',$objet);
			$files = joindre_trouver_fichier_envoye();
			if(is_array($files))
				$nouveaux_doc = $ajouter_documents('new',$files,$objet,$id_objet,$mode);
		}
		
		if(intval($id_objet) > 0){
			include_spip('action/editer_document');
			$documents_objet = sql_allfetsel('*','spip_documents as docs LEFT JOIN spip_documents_liens as liens on docs.id_document = liens.id_document','liens.objet='.sql_quote($objet).' AND liens.id_objet='.intval($id_objet));
			if(_request('titre'))
				$ancien_titre = _request('titre');
			if(_request('credits'))
				$ancien_credits = _request('credits');
			if(_request('descriptif'))
				$ancien_descriptif = _request('descriptif');
			foreach($documents_objet as $doc){
				$id_document = $doc['id_document'];
				if(_request('supprimer_'.$id_document)){
					include_spip('action/dissocier_document');
					$suppression = supprimer_lien_document($id_document, $objet, $id_objet, true);
				}else{
					$infos_doc = array();
					foreach(array('titre','credits','descriptif') as $champ){
						if(_request($champ.'_'.$id_document)){
							set_request($champ,_request($champ.'_'.$id_document));
							$infos_doc[$champ] = _request($champ.'_'.$id_document);
						}
						else{
							set_request($champ,'');
						}
					}
					$err = document_modifier($id_document, $infos_doc);
				}
			}
			if(isset($ancien_titre))
				set_request('titre',$ancien_titre);
			if(isset($ancien_credits))
				set_request('credits',$ancien_credits);
			if(isset($ancien_descriptif))
				set_request('descriptif',$ancien_descriptif);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_objets (Diogene)
 * 
 * Ajout des documents comme champs supplémentaires possible sur les articles
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_documents_diogene_objets($flux){
	$flux['article']['champs_sup']['documents'] = _T('medias:info_documents');
	if(defined('_DIR_PLUGIN_PAGES'))
		$flux['page']['champs_sup']['documents'] = _T('medias:info_documents');
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_champs_texte (Diogene)
 * 
 * Ajout du squelette permettant de configurer les éléments supplémentaires liés aux documents :
 * - les champs éditables de chaque document
 * - le nombre maximal de documents
 * 
 * @param array $flux
 * @return array
 */
function diogene_documents_diogene_champs_texte($flux){
	$champs = $flux['args']['champs_ajoutes'];
	if((is_array($champs) OR is_array($champs = unserialize($champs)))
		&& in_array('documents',$champs)){
		$flux['data'] .= recuperer_fond('prive/diogene_documents_champs_texte', $flux['args']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_champs_pre_edition (Diogene)
 * 
 * Ajoute la prise en compte des champs insérés dans le diogène
 * 
 * @param array $array
 * @return array
 */
function diogene_documents_diogene_champs_pre_edition($array){
	$array[] = 'champs_documents';
	$array[] = 'nombre_documents';
	return $array;
}

?>
