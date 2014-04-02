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
		
		$evenement['repetition'] = array();
		if (intval($id_objet)){
			$evenement = sql_fetsel('*','spip_evenements','id_article='.intval($id_objet));
			unset($evenement['titre']);
			unset($evenement['statut']);
			unset($evenement['id_article']);
			$repetitons = sql_allfetsel("date_debut","spip_evenements","id_evenement_source=".intval($id_evenement),'','date_debut');
			foreach($repetitons as $d)
				$valeurs['repetitions'][] = date('d/m/Y',strtotime($d['date_debut']));
		}else{
			$t=time();
			$evenement["date_debut"] = date('Y-m-d H:i:00',$t);
			$evenement["date_fin"] = date('Y-m-d H:i:00',$t+3600);
			$evenement['horaire'] = 'oui';
			$evenement['repetitions'] = array();
		}
		$evenement['repetitions'] = implode(',',$evenement['repetitions']);

		// dispatcher date et heure
		list($evenement["date_debut"],$evenement["heure_debut"]) = explode(' ',date('d/m/Y H:i',strtotime($evenement["date_debut"])));
		list($evenement["date_fin"],$evenement["heure_fin"]) = explode(' ',date('d/m/Y H:i',strtotime($evenement["date_fin"])));
	
		// traiter specifiquement l'horaire qui est une checkbox
		if (_request('date_debut') AND !_request('horaire'))
			$evenement['horaire'] = 'oui';
		$flux['args']['contexte'] = array_merge($flux['args']['contexte'],$evenement);
		$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_agenda',$flux['args']['contexte']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_vérifier
 * Fonction s'exécutant à la vérification du formulaire 
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_agenda_diogene_verifier($flux){
	$id_diogene = _request('id_diogene');
	if(intval($id_diogene)){
		$champs_ajoutes = unserialize(sql_getfetsel("champs_ajoutes","spip_diogenes","id_diogene=".intval($id_diogene)));
		$erreurs = $flux['args']['erreurs'];
		// On teste si les groupes obligatoires sont ok
		if (is_array($champs_ajoutes) && in_array('agenda',$champs_ajoutes)){
			include_spip('formulaires/editer_evenement');
			$erreurs = formulaires_editer_evenement_verifier_dist(_request('id_evenement'), $id_article,false, false, 'evenements_edit_config');
			unset($erreurs['id_parent']);
		}
		$flux['data'] = array_merge($flux['data'], $erreurs);
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
	$pipeline = pipeline('diogene_objets');
	if (in_array($flux['args']['type'],array_keys($pipeline)) && isset($pipeline[$flux['args']['type']]['champs_sup']['agenda']) AND ($id_diogene = _request('id_diogene'))) {
		$id_article = $flux['args']['id_objet'];
		/**
		 * On a un id_evenement => on met à jour
		 */
		if(_request('id_evenement')){
			include_spip('formulaires/editer_evenement');
			formulaires_editer_evenement_traiter_dist(_request('id_evenement'), $id_article,false, false, 'evenements_edit_config');
		}
	}
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