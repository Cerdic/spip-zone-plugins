<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion de contenu avant le formulaire
 * Le js du sélecteur générique
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_gerer_auteurs_diogene_avant_formulaire($flux){
    if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('auteurs',unserialize($flux['args']['champs_ajoutes'])) && ($flux['args']['type'] != 'page')){
    	$flux['data'] .= recuperer_fond('prive/diogene_gerer_auteurs_avant_formulaire', $flux['args']);
    }
    return $flux;
}

/**
 * Insertion dans le formulaire diogene_ajouter_saisies
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_gerer_auteurs_diogene_ajouter_saisies($flux){
	if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('auteurs',unserialize($flux['args']['champs_ajoutes']))){
		$id_article = $flux['args']['contexte']['id_article'];
		if(is_numeric($id_article)){
			include_spip('inc/autoriser');
			if(!autoriser('modifier','article',$id_article,$visiteur_session,$opt)){
	    		return $flux;
			}
			
			$nb_auteurs = sql_countsel('spip_auteurs','statut < 7');
			if($nb_auteurs > 1){
				$auteurs = sql_select("auteur.nom, auteur.id_auteur,auteur.statut","spip_auteurs as auteur LEFT join spip_auteurs_articles as auteur_lien USING(id_auteur)","auteur.id_auteur!=".intval($visiteur_session['id_auteur'])." AND auteur_lien.id_article=".intval($id_article));
				while($auteur = sql_fetch($auteurs)){
					$auteur_uniques[$auteur['id_auteur']] = $auteur['nom'];
				}
				if(is_array($auteur_uniques) AND (count($auteur_uniques) > 0)){
					$flux['args']['contexte']['diogene_gerer_auteurs_remove'] = $auteur_uniques;
				}
				$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_medias_gerer_auteurs',$flux['args']['contexte']);
			}
		}
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
function diogene_gerer_auteurs_diogene_traiter($flux){
	global $visiteur_session;

	$id_objet = $flux['args']['id_objet'];
	$type = $flux['args']['type'];
	$table  = $flux['args']['table'];
	$id_table_objet = id_table_objet($type);
	if($type == 'article'){
		include_spip('inc/autoriser');
    	if(!autoriser('modifier','article',$id_objet,$visiteur_session,$opt)){
    		return $flux;
		}

		if(_request('diogene_gerer_id_auteurs') OR is_array(_request('diogene_gerer_auteurs_remove'))){
			include_spip('inc/invalideur');

			if(_request('diogene_gerer_id_auteurs')){
				/**
				 * Insertion des auteurs
				 */
				include_spip('action/editer_auteurs');
				$ajout = ajouter_auteur_et_rediriger('article', $id_objet, _request('diogene_gerer_id_auteurs'), '');
				suivre_invalideur("id='id_auteur/"._request('diogene_gerer_id_auteurs')."'",true);
			}
			if(is_array(_request('diogene_gerer_auteurs_remove'))){
				/**
				 * Suppression des auteurs si demandée
				 */
				include_spip('action/editer_auteurs');
				foreach(_request('diogene_gerer_auteurs_remove') as $id_auteur){
					if(($id_auteur == $visiteur_session['id_auteur']) && ($visiteur_session['statut'] != '0minirezo')){
						/**
						 * On ne peut pas s'enlever soit même des auteurs si l'on n'est pas admin
						 */
					}else{
						$suppr = supprimer_auteur_et_rediriger('article', $id_objet, $id_auteur, '');
						suivre_invalideur("id='id_auteur/$id_auteur'",true);
					}
				}
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_champs_sup
 * On ajoute le champ auteur dans les articles
 * 
 * @param array $flux La liste des champs pour les diogenes
 */
function diogene_gerer_auteurs_diogene_champs_sup($flux){
	$flux['article']['auteurs'] = $flux['page']['auteurs'] = _T('diogene_gerer_auteurs:label_cfg_ajout_auteurs');
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head
 * On insert les js du séleceteur générique si ils ne le sont pas déjà
 *.
 * @param string $flux
 */
function diogene_gerer_auteurs_insert_head($flux){
	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);
	return $flux;
}
?>