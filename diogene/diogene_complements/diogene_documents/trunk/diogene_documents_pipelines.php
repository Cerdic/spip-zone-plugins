<?php
/**
 * Plugin Diogene Documents
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 
 * © 2014-2015 - Distribue sous licence GNU/GPL
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

		$flux['args']['contexte']['nombre_documents'] = isset($flux['args']['options_complements']['nombre_documents']) ? $flux['args']['options_complements']['nombre_documents'] : 0;

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

		if(isset($flux['args']['options_complements']['documents_un_par_un']) && $flux['args']['options_complements']['documents_un_par_un'] == 'on' && intval($flux['args']['options_complements']['nombre_documents']) >= 1)
			$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_documents_un_par_un',$flux['args']['contexte']);
		else
			$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_documents',$flux['args']['contexte']);
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
			$files = diogene_document_joindre_trouver_fichier_envoye(array($post['fichier_upload']));
			if(is_array($files)){
				$nouveaux_doc = $ajouter_documents('new',$files,$objet,$id_objet,$mode);
				foreach($files as $i => $file){
					$infos_doc = array();
					if($file['titre'])
						$infos_doc['titre'] = $file['titre'];
					if($file['descriptif'])
						$infos_doc['descriptif'] = $file['descriptif'];
					if($file['credits'])
						$infos_doc['credits'] = $file['credits'];
					if(count($infos_doc) > 0){
						$test = sql_updateq('spip_documents',$infos_doc,'id_document='.$nouveaux_doc[$i]);
					}
				}
			}
		}
		
		if(intval($id_objet) > 0){
			include_spip('action/editer_document');
			$documents_objet = sql_allfetsel('id_document','spip_documents_liens','objet='.sql_quote($objet).' AND id_objet='.intval($id_objet));
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
							$valeur_champ = _request($champ.'_'.$id_document);
							set_request($champ,$valeur_champ);
							$infos_doc[$champ] = $valeur_champ;
						}
						else
							set_request($champ,'');
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

function diogene_document_joindre_trouver_fichier_envoye($post){
	if (is_array($post)){
		$i = 1;
		include_spip('action/ajouter_documents');
		foreach ($post as $file) {
			if (is_array($file['name'])){
				while (count($file['name'])){
						$test=array(
							'error'=>array_shift($file['error']),
							'name'=>array_shift($file['name']),
							'tmp_name'=>array_shift($file['tmp_name']),
							'type'=>array_shift($file['type']),
							);
						if (!($test['error'] == 4)){
							if (is_string($err = joindre_upload_error($test['error'])))
								return $err; // un erreur upload
							if (!is_array(verifier_upload_autorise($test['name'])))
								return _T('medias:erreur_upload_type_interdit',array('nom'=>$test['name']));
							if(_request('titre_document'.$i))
								$test['titre'] = _request('titre_document'.$i);
							if(_request('descriptif_document'.$i))
								$test['descriptif'] = _request('descriptif_document'.$i);
							if(_request('credits_document'.$i))
								$test['credits'] = _request('credits_document'.$i);
							$files[]=$test;
						}
						$i++;
				}
			}
			else {
				//UPLOAD_ERR_NO_FILE
				if (!($file['error'] == 4)){
					if (is_string($err = joindre_upload_error($file['error'])))
						return $err; // un erreur upload
					if (!is_array(verifier_upload_autorise($file['name'])))
						return _T('medias:erreur_upload_type_interdit',array('nom'=>$file['name']));
					$files[]=$file;
				}
			}
		}
		if (!count($files))
			return _T('medias:erreur_indiquez_un_fichier');
	}
	return $files;
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
 * Ajoute la prise en compte des champs insérés dans le diogène :
 * - champs_documents : tableau de champs associés aux documents dans l'interface de saisie
 * - nombre_documents : nombre maximal de documents pouvant être lié
 * - documents_un_par_un : affichera dans le formulaire autant de bouton parcourir que de documents possibles
 * 
 * @param array $array
 * @return array
 */
function diogene_documents_diogene_champs_pre_edition($array){
	$array[] = 'champs_documents';
	$array[] = 'nombre_documents';
	$array[] = 'documents_un_par_un';
	return $array;
}

?>
