<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (plugin Diogene)
 * On ajoute simplement le selecteur de licences dans le formulaire
 * 
 * @param array $flux Le contexte d'environnement
 */
function diogene_notation_diogene_ajouter_saisies($flux){
	if(defined('_DIR_PLUGIN_NOTATION') && is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('notation',unserialize($flux['args']['champs_ajoutes']))){
    	$flux['data'] .= recuperer_fond('formulaires/diogene_notation',$flux['args']['contexte']);
	}
    return $flux;
}

/**
 * Insertion dans le pipeline diogene_verifier (plugin Diogene)
 * On ajoute une vérification de la notation
 * 
 * @param array $flux Le contexte d'environnement
 */
function diogene_notation_diogene_verifier($flux){
	$erreurs = &$flux['args']['erreurs'];

	if(defined('_DIR_PLUGIN_NOTATION') && !$erreurs['accepter_note'] && ($accepter_note = _request('accepter_note'))){
		if((!empty($accepter_note)) && !in_array($accepter_note,array('oui','non'))){
			$flux['data']['accepter_note'] = _T('diogene_notation:erreur_valeur_inexacte');
		}
	}

	return $flux;
}

/**
 * Insertion dans le pipeline diogene_traiter (plugin Diogene)
 * On ajoute la notation dans les champs à enregistrer
 * 
 * @param array $flux Le contexte d'environnement
 */
function diogene_notation_diogene_traiter($flux){
	if(defined('_DIR_PLUGIN_NOTATION') && ($accepter_note = _request('accepter_note'))){
		$flux['data']['accepter_note'] = $accepter_note;
	}
	return $flux;
}
/**
 * Insertion dans le pipeline diogene_objets (plugin Diogene)
 * On ajoute la possibilité d'avoir une partie de formulaire pour notation pour les articles, 
 * les pages spécifiques et emballe_medias
 * 
 * @param array $flux Le contexte du flux
 */
function diogene_notation_diogene_objets($flux){
	if(defined('_DIR_PLUGIN_NOTATION')){
		$flux['article']['champs_sup']['notation'] = _T('notation:notation');
		if(defined('_DIR_PLUGIN_PAGES'))
			$flux['page']['champs_sup']['notation'] = _T('notation:notation');
	}
	return $flux;
}

?>