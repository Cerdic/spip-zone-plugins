<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (plugin Diogene)
 * On ajoute simplement le selecteur de licences dans le formulaire
 * 
 * @param array $flux Le contexte d'environnement
 */
function diogene_licence_diogene_ajouter_saisies($flux){
	if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('licence',unserialize($flux['args']['champs_ajoutes']))){
		include_spip('inc/licence');
		$flux['args']['contexte']['licences'] = $GLOBALS['licence_licences'];
		$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_medias_licence',$flux['args']['contexte']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_verifier (plugin Diogene)
 * On ajoute une vérification de la licence
 * 
 * @param array $flux Le contexte d'environnement
 */
function diogene_licence_diogene_verifier($flux){
	$id_article = _request('id_article');
	$erreurs = $flux['args']['erreurs'];

	if(!$erreurs['id_licence'] && ($licence = _request('id_licence'))){
		if((!empty($licence)) && !is_numeric($licence)){
			$flux['data']['id_licence'] = _T('diogene:valeur_pas_float',array('champs'=> _T('licence:licence')));
		}
	}

	return $flux;
}

/**
 * Insertion dans le pipeline diogene_traiter (plugin Diogene)
 * On ajoute la licence dans les champs à enregistrer
 * 
 * @param array $flux Le contexte d'environnement
 */
function diogene_licence_diogene_traiter($flux){
	$id_objet = $flux['args']['id_objet'];
	if(intval($id_objet) && ($licence = _request('id_licence'))){
		$flux['data']['id_licence'] = $licence;
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_objets
 * On ajoute la possibilité de prise en compte des licences sur les articles
 * 
 * @param array $flux Un tableau des champs que l'on peut ajouter aux formulaires
 * @return array $flux Le tableau des champs complétés
 */
function diogene_licence_diogene_objets($flux){
	$flux['article']['champs_sup']['licence'] = _T('diogene_licence:form_legend');
	if(defined('_DIR_PLUGIN_PAGES'))
		$flux['page']['champs_sup']['licence'] = _T('diogene_licence:form_legend');
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_champs_texte (plugin Diogene)
 * On ajoute dans le formulaire d'édition de diogène la possibilité de choisir une licence par défaut
 * Utile pour les objets qui ont toujours la même licence
 * 
 * @param array $flux Le contexte du flux
 */
function diogene_licence_diogene_champs_texte($flux){
	if(in_array($flux['args']['objet'],array('article','page','emballe_media'))){
		include_spip('inc/licence');
		$flux['args']['licences'] = $GLOBALS['licence_licences'];
		$flux['data'] .= recuperer_fond('formulaires/diogene_licence_defaut',$flux['args']);
	}
	return $flux;
}

function diogene_licence_diogene_champs_pre_edition($array){
	$array[] = 'id_licence_defaut';
	return $array;
}
?>
