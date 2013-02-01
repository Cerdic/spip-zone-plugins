<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (Diogene)
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_mots_diogene_ajouter_saisies($flux){
	$id_article = $flux['args']['contexte']['id_article'];
	if (is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('mots',unserialize($flux['args']['champs_ajoutes']))) {

		if (!is_array(unserialize($flux['args']['options_complements']['mots_obligatoires']))) {
			$mots_obligatoires = array();
		} else {
			$mots_obligatoires = unserialize($flux['args']['options_complements']['mots_obligatoires']);
		}
		if(!is_array(unserialize($flux['args']['options_complements']['mots_facultatifs']))) {
			$mots_facultatifs = array();
		} else {
			$mots_facultatifs = unserialize($flux['args']['options_complements']['mots_facultatifs']);
		}
		$valeurs_mots['id_groupes'] = $groupes_possibles = array_merge($mots_obligatoires,$mots_facultatifs);
		
		if (intval($id_article)) {				
			//On récupère les mots qui sont peut être associés
			foreach($groupes_possibles as $groupe){
				if (sql_getfetsel('unseul','spip_groupes_mots','id_groupe='.intval($groupe))== 'oui') {
					$valeurs_mots['groupe_'.$groupe] = sql_fetsel('mot.id_mot','spip_mots as mot left join spip_mots_articles as mots_articles ON (mot.id_mot=mots_articles.id_mot)','mots_articles.id_article='.intval($id_article).' AND mot.id_groupe='.intval($groupe));
				}else {
					$result = sql_select('mot.id_mot','spip_mots as mot left join spip_mots_articles as articles ON mot.id_mot=articles.id_mot','id_groupe='.intval($groupe).' AND id_article='.intval($id_article));
					while ($row = sql_fetch($result)) {
						$valeurs_mots['groupe_'.$groupe][] = $row['id_mot'];
					}
				}
			}
		}
		
		if (is_array($valeurs_mots)) {
			$flux['args']['contexte'] = array_merge($flux['args']['contexte'],$valeurs_mots);
		}
		
		$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_medias_mots',$flux['args']['contexte']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_verifier (Diogene)
 * Vérification des formulaires qui sont modifiés par Diogene
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_mots_diogene_verifier($flux){
	$id_diogene = _request('id_diogene');
	if(intval($id_diogene)){
		$diogene = sql_fetsel("*","spip_diogenes","id_diogene=$id_diogene");
		$options_complements = unserialize($diogene['options_complements']);
		$erreurs = $flux['args']['erreurs'];
		// On teste si les groupes obligatoires sont ok
		if (is_array($options_complements['mots_obligatoires'])) {
			foreach($options_complements['mots_obligatoires'] as $groupe_obligatoire){
				$mots_groupe = _request('groupe_'.$groupe_obligatoire);
				if(empty($mots_groupe)){
					$erreurs['groupe_'.$groupe_obligatoire] = _T('info_obligatoire');
				}
			}
		}
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
function diogene_mots_diogene_traiter($flux){
	if (($flux['args']['type'] == 'article') AND ($id_diogene = _request('id_diogene'))) {
		$id_article = $flux['args']['id_objet'];

		$diogene = sql_fetsel("*","spip_diogenes","id_diogene=".intval($id_diogene));
		$options_complements = unserialize($diogene['options_complements']);

		$mots_obligatoires = is_array(unserialize($options_complements['mots_obligatoires']))
			? unserialize($options_complements['mots_obligatoires'])
			: array();
			
		$mots_facultatifs = is_array(unserialize($options_complements['mots_facultatifs']))
			? unserialize($options_complements['mots_facultatifs'])
			: array();

		/**
		 * On traite les mots clés obligatoires ou pas
		 */ 
		include_spip('inc/editer_mots');
		$groupes_possibles = array_merge($mots_obligatoires,$mots_facultatifs);
		
		$mots_multiples = array();
		
		/**
		 * On traite chaque groupe séparément
		 * Si c'est une modification d'article il se peut qu'il faille supprimer les anciens mots
		 * On fait une vérifications sur chaque groupe
		 */
		foreach($groupes_possibles as $groupe){
			/**
			 * Si le select est multiple
			 */
			if(is_array(_request('groupe_'.$groupe))){
				$result = sql_select('0+mot.titre AS num, mot.id_mot','spip_mots as mot left join spip_mots_articles as articles ON mot.id_mot=articles.id_mot','id_groupe='.intval($groupe).' AND id_article='.intval($id_article),'','num, mot.titre');
				while ($row = sql_fetch($result)) {
					$mots_multiples[] = $row['id_mot'];
				}
				foreach(_request('groupe_'.$groupe) as $cle => $mot){
					/**
					 * Si le mot est déja dans les mots, on le supprime juste
					 * de l'array des mots originaux
					 */
					if(in_array($mot, $mots_multiples)){
						unset($mots_multiples[$cle]);
					}
					else{
						sql_insertq('spip_mots_articles', array('id_mot' =>$mot,  'id_article' => $id_article));
					}
				}
			}
			/**
			 * Si le select est simple
			 */
			else{
				if(!is_array($mots_uniques = sql_fetsel('mot.id_mot','spip_mots as mot left join spip_mots_articles as mots_articles ON (mot.id_mot=mots_articles.id_mot)','mots_articles.id_article='.intval($id_article).' AND mot.id_groupe='.intval($groupe))))
					$mots_uniques = array();
				if(in_array(_request('groupe_'.$groupe), $mots_uniques)){
					unset($mots_uniques);
				}
				else{
					sql_insertq('spip_mots_articles', array('id_mot' =>_request('groupe_'.$groupe),  'id_article' => $id_article));
				}
			}
			/**
			 * S'il reste quelque chose dans les mots d'origine, on les délie de l'article
			 */
			if(count($mots_uniques)>0){
				sql_delete('spip_mots_articles','id_article='.intval($id_article).' AND id_mot IN ('.implode(',',$mots_uniques).')');
			}
			if(count($mots_multiples)>0){
				sql_delete('spip_mots_articles','id_article='.intval($id_article).' AND id_mot IN ('.implode(',',$mots_multiples).')');
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_champs_sup (Diogene)
 * Fonction permettant de mettre des mots dans le formulaire d'un Diogene 
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_mots_diogene_champs_sup($flux){
	$flux['article']['mots'] = _T('diogene_mots:form_legend');
	$flux['emballe_media']['mots'] = _T('diogene_mots:form_legend');
	return $flux;
}

function diogene_mots_diogene_champs_texte($flux){
	$champs = $flux['args']['champs_ajoutes'];
	if((is_array($champs) OR is_array($champs = unserialize($champs)))
		&& in_array('mots',$champs)){
		$flux['data'] .= recuperer_fond('prive/diogene_mots_champs_texte', $flux['args']);
	}
	return $flux;
}

function diogene_mots_diogene_champs_pre_edition($array){
	$array[] = 'mots_obligatoires';
	$array[] = 'mots_facultatifs';
	return $array;
}

?>