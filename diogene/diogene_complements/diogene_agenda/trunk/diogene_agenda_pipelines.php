<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le formulaire diogene_ajouter_saisies
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_agenda_diogene_ajouter_saisies($flux){
	if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('auteurs',unserialize($flux['args']['champs_ajoutes']))){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['contexte'][$id_table_objet];
		
		$flux['args']['contexte']['agenda_caches'] = array();

		if(is_array(unserialize($flux['args']['options_complements']['agenda_caches'])))
			$flux['args']['contexte']['agenda_caches'] = unserialize($flux['args']['options_complements']['agenda_caches']);

		$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_agenda',$flux['args']['contexte']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_traiter
 * Fonction s'exécutant au traitement du formulaire 
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_agenda_diogene_traiter($flux){
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_objets
 * On ajoute l'agenda dans les articles
 * 
 * @param array $flux La liste des champs pour les diogenes
 */
function diogene_agenda_diogene_objets($flux){
	$flux['article']['champs_sup']['agenda'] = _T('diogene_agenda:label_cfg_ajout_agenda');
	return $flux;
}

function diogene_agenda_diogene_champs_texte($flux){
	$champs = $flux['args']['champs_ajoutes'];
	if((is_array($champs) OR is_array($champs = unserialize($champs)))
		&& in_array('agenda',$champs)){
		$flux['data'] .= recuperer_fond('prive/diogene_agenda_champs_texte', $flux['args']);
	}
	return $flux;
}

function diogene_agenda_diogene_champs_pre_edition($array){
	$array[] = 'agenda_caches';
	return $array;
}

function diogene_agenda_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('css/diogene_agenda.css')).'" type="text/css" media="all" />';
	return $flux;
}
?>