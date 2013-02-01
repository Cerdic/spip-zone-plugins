<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_avant_formulaire (Plugin Diogene)
 * On insère du contenu avant le formulaire d'édition d'un objet
 * Le js du sélecteur générique
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_spipicious_diogene_avant_formulaire($flux){
    global $visiteur_session;
    if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('spipicious',unserialize($flux['args']['champs_ajoutes'])) && ($flux['args']['type'] != 'page')){
		include_spip('inc/autoriser');
    	if(autoriser('tagger_spipicious','article',$id_objet,$visiteur_session,$opt)){
    		$flux['data'] .= recuperer_fond('prive/diogene_spipicious_avant_formulaire', $flux['args']);
		}
    }
    return $flux;
}

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (plugin Diogene)
 * On ajoute les saisies nécessaires au formulaire
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_spipicious_diogene_ajouter_saisies($flux){
	global $visiteur_session;
	$id_article = $flux['args']['contexte']['id_article'];
	if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('spipicious',unserialize($flux['args']['champs_ajoutes']))){
		include_spip('inc/autoriser');
    	if(autoriser('tagger_spipicious','article',$id_article,$visiteur_session,$opt)){
	    	if(intval($id_article)){
				$tags = sql_select("mots.id_mot, mots.titre","spip_spipicious as spipicious LEFT join spip_mots as mots USING(id_mot)","spipicious.id_auteur=".intval($visiteur_session['id_auteur'])." AND spipicious.id_objet=".intval($id_article)." AND spipicious.objet='article'");
				while($tag = sql_fetch($tags)){
					$tag_uniques[$tag['id_mot']] = $tag['titre'];
				}
				if(is_array($tag_uniques)){
					$flux['args']['contexte']['diogene_spipicious_removal_tags'] = $tag_uniques;
				}
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
	global $visiteur_session;
	if($flux['args']['action']=='modifier'){

		$id_objet = $flux['args']['id_objet'];
		$type = $flux['args']['type'];
		$table  = $flux['args']['table'];
		$id_table_objet = id_table_objet($type);

		include_spip('inc/autoriser');
    	if(!autoriser('tagger_spipicious',$type,$id_objet,$visiteur_session,$opt)){
    		return $flux;
		}

		include_spip('inc/invalideur');
		$id_groupe = lire_config('spipicious/groupe_mot','1');
		if($tags = _request('diogene_spipicious_tags')){
			/**
			 * Insertion des tags
			 */
			include_spip('action/spipicious_ajouter_tags');
			$tableau_tags = explode(";",$tags);
			$ajout = spipicious_ajouter_tags($tableau_tags,$visiteur_session['id_auteur'],$id_objet,$type,$id_table_objet,'spip_mots_'.$type.'s',$id_groupe,'oui');

			foreach($tableau_tags as $id_tag){
				suivre_invalideur("id='id_mot/$id_mot'",true);
			}
		}
		if(is_array(_request('diogene_spipicious_removal_tags'))){
			/**
			 * Suppression des tags si demandée
			 */
			include_spip('action/spipicious_supprimer_tags');
			$suppression = spipicious_supprimer_tags(_request('diogene_spipicious_removal_tags'),$visiteur_session['id_auteur'],$id_objet,$type,$id_table_objet,'spip_mots_'.$type.'s',$id_groupe);
			foreach(_request('diogene_spipicious_removal_tags') as $id_tag){
				suivre_invalideur("id='id_mot/$id_mot'",true);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_champs_sup (Plugin Diogene)
 * On ajoute la possibilité de prise en compte des tags sur :
 * -* Les articles
 * -* Les articles de type emballe_medias
 * 
 * @param array $flux Un tableau des champs que l'on peut ajouter aux formulaires
 * @return array $flux Le tableau de champs complété
 */
function diogene_spipicious_diogene_champs_sup($flux){
	$flux['article']['spipicious'] = $flux['page']['spipicious']  = _T('diogene_spipicious:tags_spipicious');
	return $flux;
}
?>