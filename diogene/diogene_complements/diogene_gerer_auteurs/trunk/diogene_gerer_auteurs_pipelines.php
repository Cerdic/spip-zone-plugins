<?php
/**
 * Plugin Diogene Auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010-2014 - Distribue sous licence GNU/GPL
 *
 * Utilisation des pipelines par Diogene Auteurs
 *
 * @package SPIP\Diogene Auteurs\Pipelines
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_avant_formulaire (Diogène)
 * 
 * Insertion de contenu avant le formulaire
 * Le js du sélecteur générique
 *
 * @param array $flux 
 * 	Le contexte du pipeline
 * @return array $flux 
 * 	Le contexte modifié passé aux suivants
 */
function diogene_gerer_auteurs_diogene_avant_formulaire($flux){
	if(isset($flux['args']['id']) && is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('auteurs',unserialize($flux['args']['champs_ajoutes'])) && ($flux['args']['type'] != 'page'))
		$flux['data'] .= recuperer_fond('prive/diogene_gerer_auteurs_avant_formulaire', $flux['args']);
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (Diogène)
 * 
 * On ajoute la partie du formulaire concernant les auteurs si nécessaire
 * 
 * @param array $flux 
 * 	Le contexte du pipeline
 * @return array $flux 
 * 	Le contexte modifié passé aux suivants
 */
function diogene_gerer_auteurs_diogene_ajouter_saisies($flux){
	if(is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('auteurs',unserialize($flux['args']['champs_ajoutes']))){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['contexte'][$id_table_objet];
		$auteur_uniques = array();

		if(is_numeric($id_objet) && intval($id_objet) > 0){
			include_spip('inc/autoriser');
			if(!autoriser('associerauteurs',$objet,$id_objet))
				return $flux;
			
			$nb_auteurs = sql_countsel('spip_auteurs','statut < 7');

			if($nb_auteurs > 1){
				$auteurs = sql_allfetsel("auteur.nom, auteur.id_auteur","spip_auteurs as auteur LEFT join spip_auteurs_liens as auteur_lien ON auteur.id_auteur=auteur_lien.id_auteur","auteur.id_auteur!=".intval($GLOBALS['visiteur_session']['id_auteur'])." AND auteur_lien.objet=".sql_quote($objet)." AND auteur_lien.id_objet=".intval($id_objet));
				if($GLOBALS['visiteur_session']['statut']=='0minirezo'){
					$auteur = sql_fetsel("auteur.nom, auteur.id_auteur","spip_auteurs as auteur LEFT join spip_auteurs_liens as auteur_lien ON auteur.id_auteur=auteur_lien.id_auteur","auteur.id_auteur=".intval($GLOBALS['visiteur_session']['id_auteur'])." AND auteur_lien.objet=".sql_quote($objet)." AND auteur_lien.id_objet=".intval($id_objet));
					if(is_array($auteur))
						$flux['args']['contexte']['diogene_gerer_auteurs_remove'][$auteur['id_auteur']] = $auteur['nom'];
				}
				if(count($auteurs) > 0){
					foreach($auteurs as $auteur){
						$flux['args']['contexte']['diogene_gerer_auteurs_remove'][$auteur['id_auteur']] = $auteur['nom'];
					}
				}
				if(_request('diogene_gerer_auteurs_supprimer'))
					$flux['args']['contexte']['diogene_gerer_auteurs_supprimer'] = _request('diogene_gerer_auteurs_supprimer');
				$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_medias_gerer_auteurs',$flux['args']['contexte']);
			}
		}else{
			if($GLOBALS['visiteur_session']['statut']=='0minirezo'){
				$auteur = sql_fetsel("nom, id_auteur,statut","spip_auteurs","id_auteur=".intval($GLOBALS['visiteur_session']['id_auteur']));
				$auteur_uniques[$auteur['id_auteur']] = $auteur['nom'];
			}
			if(count($auteur_uniques) > 0)
				$flux['args']['contexte']['diogene_gerer_auteurs_remove'] = $auteur_uniques;
			$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_medias_gerer_auteurs',$flux['args']['contexte']);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_traiter (Diogène)
 * 
 * Fonction s'exécutant au traitement du formulaire
 *
 * @param array $flux
 * 	Le contexte du pipeline
 * @return array $flux 
 * 	Le contexte modifié passé aux suivants
 */
function diogene_gerer_auteurs_diogene_traiter($flux){
	$id_objet = $flux['args']['id_objet'];
	$type = $flux['args']['type'];
	$pipeline = pipeline('diogene_objets');
	if(in_array($type,array_keys($pipeline)) && isset($pipeline[$type]['champs_sup']['auteurs'])){
		include_spip('inc/autoriser');
		if(!autoriser('associerauteurs',$type,$id_objet))
			return $flux;

		if(_request('diogene_gerer_id_auteurs') OR is_array(_request('diogene_gerer_auteurs_supprimer'))){
			include_spip('inc/invalideur');
			include_spip('action/editer_auteur');
			if(_request('diogene_gerer_id_auteurs')){
				/**
				 * Insertion des auteurs
				 */
				$auteurs_associer = _request('diogene_gerer_id_auteurs');
				$ajout = auteur_associer($auteurs_associer,array($type=>$id_objet));
				suivre_invalideur("id='id_auteur/$auteurs_associer'",true);
			}
			if(is_array(_request('diogene_gerer_auteurs_supprimer'))){
				/**
				 * Suppression des auteurs si demandée
				 */
				foreach(_request('diogene_gerer_auteurs_supprimer') as $id_auteur){
					if(($id_auteur == $GLOBALS['visiteur_session']['id_auteur']) && ($GLOBALS['visiteur_session']['statut'] != '0minirezo')){
						/**
						 * On ne peut pas s'enlever soit même des auteurs si l'on n'est pas admin
						 */
					}else{
						$suppr = auteur_dissocier($id_auteur,array($type=>$id_objet));
						suivre_invalideur("id='id_auteur/$id_auteur'",true);
					}
				}
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_objets (Diogène)
 * 
 * On ajoute la possibilité d'ajouter le champ auteur dans les articles
 * dans la configuration d'un Diogène
 * 
 * @param array $flux 
 * 	La liste des champs pour les diogenes
 * @return array $flux
 * 	La liste des champs modifiée
 */
function diogene_gerer_auteurs_diogene_objets($flux){
	$flux['article']['champs_sup']['auteurs'] = _T('diogene_gerer_auteurs:label_cfg_ajout_auteurs');
	if(defined('_DIR_PLUGIN_PAGES'))
		$flux['page']['champs_sup']['auteurs'] = _T('diogene_gerer_auteurs:label_cfg_ajout_auteurs');
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * 
 * On insert les js du sélecteur générique si ils ne le sont pas déjà
 *.
 * @param string $flux
 * 	Le contenu de la balise #INSERT_HEAD
 * @return string $flux
 * 	Le contenu de la balise #INSERT_HEAD modifié
 */
function diogene_gerer_auteurs_insert_head($flux){
	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);
	return $flux;
}
?>