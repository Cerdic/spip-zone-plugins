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
	if (is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('mots',unserialize($flux['args']['champs_ajoutes']))){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['contexte'][$id_table_objet];

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
		// Traitement
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
