<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le formulaire diogene_ajouter_saisies
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_agenda_diogene_ajouter_saisies($flux){
	if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('agenda',unserialize($flux['args']['champs_ajoutes']))){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['contexte'][$id_table_objet];

		$flux['args']['contexte']['agenda_caches'] = array();
		
		if(is_array(unserialize($flux['args']['options_complements']['agenda_caches'])))
			$flux['args']['contexte']['agenda_caches'] = unserialize($flux['args']['options_complements']['agenda_caches']);
		
		$evenement = array();
		$evenement['repetitions'] = array();
		if(intval($id_objet)){
			$evenement = sql_fetsel('*','spip_evenements','id_article='.intval($id_objet).' AND statut != "poubelle"');
			if($evenement['titre'] != sql_getfetsel('titre','spip_evenements','id_article='.intval($id_objet)))
				$evenement['titre_evenement'] = $evenement['titre'];
			unset($evenement['titre']);
			unset($evenement['statut']);
			unset($evenement['id_article']);
			if(intval($evenement['id_evenement']) > 0){
				$repetitons = sql_allfetsel("date_debut","spip_evenements","id_evenement_source=".intval($evenement['id_evenement']),'','date_debut');
				foreach($repetitons as $d){
					$evenement['repetitions'][] = date('d/m/Y',strtotime($d['date_debut']));
				}
			}
			else
				$evenement = array();
		}
		
		/**
		 * Quels sont les champs à charger depuis l'environnement lors du chargement
		 * Par exemple lorsqu'il y a une erreur dans verifier :
		 * Les champs editables de la table "evenements"
		 * On ajoute manuellement heure_debut et heure_fin
		 */
		$champs = objet_info('evenement','champs_editables');
		$champs[] = 'heure_debut';
		$champs[] = 'heure_fin';
		$champs[] = 'titre_evenement';
		foreach($champs as $champ){
			if(_request($champ))
				$evenement[$champ] = _request($champ);
		}
		
		if(count($evenement['repetitions']) > 0)
			$evenement['repetitions'] = implode(',',$evenement['repetitions']);

		if(isset($flux['args']['options_complements']['agenda_legende']) && strlen($flux['args']['options_complements']['agenda_legende']) > 0){
			$evenement['agenda_legende'] = $flux['args']['options_complements']['agenda_legende'];
		}
		/**
		 * dispatcher date et heure s'ils existent
		 * On ne le fait que si :
		 * - date_debut et date_fin existent
		 * - date_debut et date_fin sont des dates mysql (pas récupérées de l'environnement)
		 */
		if($evenement["date_debut"] && preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',$evenement["date_debut"]))
			list($evenement["date_debut"],$evenement["heure_debut"]) = explode(' ',date('d/m/Y H:i',strtotime($evenement["date_debut"])));

		if($evenement["date_fin"] && preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',$evenement["date_fin"]))
			list($evenement["date_fin"],$evenement["heure_fin"]) = explode(' ',date('d/m/Y H:i',strtotime($evenement["date_fin"])));

		// traiter specifiquement l'horaire qui est une checkbox
		if (_request('date_debut') AND !_request('horaire'))
			$evenement['horaire'] = 'oui';
		
		if(isset($flux['args']['options_complements']['agenda_obligatoire']) && $flux['args']['options_complements']['agenda_obligatoire'] == 'on')
			$evenement['agenda_obligatoire'] = "on";
		$evenement['supprimer_evenement'] = _request('supprimer_evenement');
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
	if(intval($id_diogene) && !_request('supprimer_evenement')){
		$champs_ajoutes = unserialize(sql_getfetsel("champs_ajoutes","spip_diogenes","id_diogene=".intval($id_diogene)));
		$erreurs = $flux['args']['erreurs'];
		if (is_array($champs_ajoutes) && in_array('agenda',$champs_ajoutes)){
			include_spip('formulaires/editer_evenement');
			$erreurs = formulaires_editer_evenement_verifier_dist(_request('id_evenement'), $id_article,false, false, 'evenements_edit_config');
			unset($erreurs['id_parent']);
			
			if(!isset($flux['args']['options_complements']['agenda_obligatoire']) OR $flux['args']['options_complements']['agenda_obligatoire'] != 'on'){
				$champs_post = false;
				$champs = objet_info('evenement','champs_editables');
				$champs[] = 'titre_evenement';
				foreach($champs as $champ){
					if(!in_array($champ,array('titre','horaire')) && _request($champ)){
						$champs_post = true;
						break;
					}
				}
				if(count($erreurs) > 0 && !$champs_post)
					$erreurs = array();
			}
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
		if(intval($id_article) > 0 && !_request('supprimer_evenement')){
			if(!isset($flux['args']['options_complements']['agenda_obligatoire']) OR $flux['args']['options_complements']['agenda_obligatoire'] != 'on'){
				$champs_post = false;
				foreach(objet_info('evenement','champs_editables') as $champ){
					if(!in_array($champ,array('titre','horaire')) && _request($champ)){
						$champs_post = true;
						break;
					}
				}
				if(!$champs_post)
					return $flux;
			}
			include_spip('formulaires/editer_evenement');
			set_request('id_parent',$id_article);
			
			/**
			 * On fait attention à donner le titre de l'évènement si envoyé
			 * sinon ce sera le titre de l'article
			 */
			$titre_article = false;
			if(strlen(_request('titre_evenement')) > 0){
				$titre_article = _request('titre');
				set_request('titre',_request('titre_evenement'));
			}
			
			/**
			 * On fait attention à donner un statut existant au statut
			 */
			$statuts = array_keys(objet_info('evenement','statut_titres'));
			if(!in_array(_request('statut'),$statuts)){
				$statut_article = _request('statut');
				set_request('statut',$statuts[0]);
			}

			if(intval(_request('id_evenement')) > 0)
				formulaires_editer_evenement_traiter_dist(_request('id_evenement'), $id_article,false, false, 'evenements_edit_config');
			else
				formulaires_editer_evenement_traiter_dist('new', $id_article,false, false, 'evenements_edit_config');
			
			/**
			 * On remet le statut et le titre de l'article dans l'environnement du $_POST
			 */
			if($statut_article)
				set_request('statut',$statut_article);
			if($titre_article)
				set_request('titre',$titre_article);
		}
		// Supprimer l'évènement
		else{
			include_spip('action/editer_evenement');
			evenement_instituer(_request('id_evenement'), array('statut'=>'poubelle','id_article'=>$id_article));
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
	$array[] = 'agenda_legende';
	$array[] = 'agenda_obligatoire';
	return $array;
}

function diogene_agenda_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('css/diogene_agenda.css')).'" type="text/css" media="all" />';
	return $flux;
}
?>