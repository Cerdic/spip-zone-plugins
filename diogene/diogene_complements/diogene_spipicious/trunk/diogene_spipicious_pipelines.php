<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (plugin Diogene)
 * On ajoute les saisies nécessaires au formulaire
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_spipicious_diogene_ajouter_saisies($flux){
	if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('spipicious',unserialize($flux['args']['champs_ajoutes']))){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['contexte'][$id_table_objet];
		$flux['args']['contexte']['objet'] = $objet;
		$flux['args']['contexte']['id_objet'] = $id_objet;
		include_spip('inc/autoriser');
    	if(autoriser('tagger_spipicious',$objet,$id_objet)){
	    	if(intval($id_objet)){
				$tags = sql_select("mots.id_mot, mots.titre","spip_spipicious as spipicious LEFT join spip_mots as mots USING(id_mot)","spipicious.id_auteur=".intval($GLOBALS['visiteur_session']['id_auteur'])." AND spipicious.id_objet=".intval($id_objet)." AND spipicious.objet=".sql_quote($objet));
				while($tag = sql_fetch($tags)){
					$tag_uniques[$tag['id_mot']] = $tag['titre'];
				}
				if(is_array($tag_uniques))
					$flux['args']['contexte']['diogene_spipicious_removal_tags'] = $tag_uniques;
			}
    		$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_medias_spipicious',$flux['args']['contexte']);
		}
	}
    return $flux;
}

/**
 * Insertion dans le pipeline diogene_traiter (plugin Diogene)
 * Fonction s'exécutant au traitement du formulaire
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_spipicious_diogene_traiter($flux){
	if($flux['args']['action']=='modifier'){

		$id_objet = $flux['args']['id_objet'];
		$type = $flux['args']['type'];
		$table  = $flux['args']['table'];
		$id_table_objet = id_table_objet($type);

		include_spip('inc/autoriser');
    	if(!autoriser('tagger_spipicious',$type,$id_objet)){
    		return $flux;
		}

		include_spip('inc/invalideur');
		$id_groupe = lire_config('spipicious/groupe_mot','1');
		if($tags = _request('diogene_spipicious_tags')){
			/**
			 * Insertion des tags
			 */
			include_spip('action/spipicious_ajouter_tags');
			$tableau_tags = explode(",",$tags);
			/**
			 * On enlève titre et ctr_titre du $_POST 
			 * pour éviter une erreur dans inc/modifier
			 */
			if($ctr_titre = _request('ctr_titre')){
				$request = true;
				$titre = _request('titre');
				set_request('titre','');
				set_request('ctr_titre','');
			}
			$ajout = spipicious_ajouter_tags($tableau_tags,$GLOBALS['visiteur_session']['id_auteur'],$id_objet,$type,$id_table_objet,$id_groupe);
			/**
			 * On remet le $_POST initial
			 */
			if($request){
				set_request('titre',$titre);
				set_request('ctr_titre',$ctr_titre);
			}
			suivre_invalideur("0",true);
		}
		if(is_array(_request('diogene_spipicious_removal_tags'))){
			/**
			 * Suppression des tags si demandée
			 */
			include_spip('action/spipicious_supprimer_tags');
			$suppression = spipicious_supprimer_tags(_request('diogene_spipicious_removal_tags'),$GLOBALS['visiteur_session']['id_auteur'],$id_objet,$type,$id_table_objet,$id_groupe);
			suivre_invalideur("0",true);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_objets (Plugin Diogene)
 * On ajoute la possibilité de prise en compte des tags sur :
 * -* Les articles
 * -* Les articles Pages
 * -* Les articles de type emballe_medias
 * 
 * @param array $flux Un tableau des champs que l'on peut ajouter aux formulaires
 * @return array $flux Le tableau de champs complété
 */
function diogene_spipicious_diogene_objets($flux){
	if(defined('_DIR_PLUGIN_SPIPICIOUS')){
		$flux['article']['champs_sup']['spipicious'] = _T('diogene_spipicious:tags_spipicious');
		if(defined('_DIR_PLUGIN_PAGES')){
			$flux['page']['champs_sup']['spipicious'] = _T('diogene_spipicious:tags_spipicious');
		}
	}
	return $flux;
}
?>