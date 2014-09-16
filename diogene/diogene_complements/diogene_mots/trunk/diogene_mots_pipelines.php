<?php
/**
 * Plugin Diogene Mots
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Severo
 * 
 * © 2010-2014 - Distribue sous licence GNU/GPL
 *
 * Utilisation des pipelines par Diogene Mots
 *
 * @package SPIP\Diogene Mots\Pipelines
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (Diogene)
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_mots_diogene_ajouter_saisies($flux){
	if (is_array(unserialize($flux['args']['champs_ajoutes'])) && in_array('mots',unserialize($flux['args']['champs_ajoutes']))){
		$objet = $flux['args']['type'];
		$id_table_objet = id_table_objet($flux['args']['type']);
		$id_objet = $flux['args']['contexte'][$id_table_objet];
		$mots_obligatoires = $mots_facultatifs = array();

		if (is_array(unserialize($flux['args']['options_complements']['mots_obligatoires']))){
			$mots_obligatoires = unserialize($flux['args']['options_complements']['mots_obligatoires']);
		}

		if(is_array(unserialize($flux['args']['options_complements']['mots_facultatifs'])))
			$mots_facultatifs = unserialize($flux['args']['options_complements']['mots_facultatifs']);

		$valeurs_mots['id_groupes'] = $groupes_possibles = array_merge($mots_obligatoires,$mots_facultatifs);
		foreach($valeurs_mots['id_groupes'] as $groupe){
			if(is_array(unserialize($flux['args']['options_complements']['mot_selection_'.$groupe])))
				$valeurs_mots['mot_selection_'.$groupe] = unserialize($flux['args']['options_complements']['mot_selection_'.$groupe]);
		}
		if (intval($id_objet)){
			/**
			 * On récupère les mots qui sont postés ou peut être déjà associés
			 */
			foreach($groupes_possibles as $id_groupe){
				if(in_array($id_groupe,$mots_obligatoires))
					$valeurs_mots['groupe_obligatoire_'.$id_groupe] = 'oui';
				if (_request('groupe_'.$id_groupe)) {
					// Pour récupérer la selection courante en cas d'erreur dans vérifier() ou traiter()
					$valeurs_mots['groupe_'.$id_groupe] = _request('groupe_'.$id_groupe);
				}else if (sql_getfetsel('unseul','spip_groupes_mots','id_groupe='.intval($id_groupe)) == 'oui') {
					$valeurs_mots['groupe_'.$id_groupe] = sql_fetsel('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as mots_liens ON (mot.id_mot=mots_liens.id_mot)','mots_liens.objet='.sql_quote($objet).' AND mots_liens.id_objet='.intval($id_objet).' AND mot.id_groupe='.intval($id_groupe));
				}else {
					$result = sql_allfetsel('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as mots_liens ON mot.id_mot=mots_liens.id_mot','mots_liens.objet='.sql_quote($objet).' AND mot.id_groupe='.intval($id_groupe).' AND mots_liens.id_objet='.intval($id_objet));
					foreach ($result as $row) {
						$valeurs_mots['groupe_'.$id_groupe][] = $row['id_mot'];
					}
				}
				if (_request('nouveaux_groupe_'.$id_groupe)) {
					// Pour récupérer les nouveaux mots en cas d'erreur dans vérifier() ou traiter()
					$valeurs_mots['nouveaux_groupe_'.$id_groupe] = _request('nouveaux_groupe_'.$id_groupe);
				}
			}
		}else{
			/**
			 * On regarde juste dans l'environnement ce qui a déjà été posté
			 */
			foreach($groupes_possibles as $id_groupe){
				if(in_array($id_groupe,$mots_obligatoires)){
					$valeurs_mots['groupe_obligatoire_'.$id_groupe] = 'oui';
				}
				if (_request('groupe_'.$id_groupe)) {
					// Pour récupérer la selection courante en cas d'erreur dans vérifier() ou traiter()
					$valeurs_mots['groupe_'.$id_groupe] = _request('groupe_'.$id_groupe);
				}
			}
		}
		
		if (is_array($valeurs_mots))
			$flux['args']['contexte'] = array_merge($flux['args']['contexte'],$valeurs_mots);

		if ($flux['args']['options_complements']['montrer_titre_et_descriptif'] == 'on')
			$flux['args']['contexte'] = array_merge($flux['args']['contexte'],array('montrer_titre_et_descriptif' => $flux['args']['options_complements']['montrer_titre_et_descriptif']));

		/** 
		 * Paramètre pour permettre de créer des nouveaux mots dans les groupes choisis 
		 * TODO : seulement si l'auteur a le droit d'ajouter de nouveaux mots (peut être différent pour chaque groupe de mots ?)
		 * TODO : parametre par groupe au lieu d'un parametre general 
		 */
		 
		if ($flux['args']['options_complements']['mots_creer_dans_public'] == 'on')
			$flux['args']['contexte'] = array_merge($flux['args']['contexte'],array('mots_creer_dans_public' => $flux['args']['options_complements']['mots_creer_dans_public']));

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
		$options_complements = unserialize(sql_getfetsel("options_complements","spip_diogenes","id_diogene=".intval($id_diogene)));
		$erreurs = $flux['args']['erreurs'];
		// On teste si les groupes obligatoires sont ok
		if (isset($options_complements['mots_obligatoires']) && is_array(unserialize($options_complements['mots_obligatoires']))){
			foreach(unserialize($options_complements['mots_obligatoires']) as $groupe_obligatoire=>$id_groupe){
				$mots_groupe = _request('groupe_'.$id_groupe);
				if(empty($mots_groupe) OR is_null($mots_groupe) OR (!is_numeric($mots_groupe) && !is_array($mots_groupe))){
					$flux['data']['groupe_'.$id_groupe] = _T('info_obligatoire');
				}
			}
		}
		
		// On vérifie les mots créés par chosen
		if (test_plugin_actif('chosen')) {
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

			// Champs cachés pour la confirmation
			$champs_hidden = '';
			$erreurs = array();
			$au_moins_une_erreur = false;

			/**
			 * On traite chaque groupe séparément
			 */
			foreach($groupes_possibles as $id_groupe){
				// Lister les mots sélectionnés dans le groupe et séparer entre mots existants et mots nouveaux créés avec chosen
				$valeurs_mots_groupe = array();
				$valeurs_mots_nouveaux_groupe = array();
				$requete_groupe = is_array(_request('groupe_'.$id_groupe)) ? _request('groupe_'.$id_groupe) : array('cle' => _request('groupe_'.$id_groupe));
				$prefixe_chosen = "chosen_";
				foreach($requete_groupe as $cle => $mot){
					if (substr($mot, 0, strlen($prefixe_chosen)) == $prefixe_chosen) {
						// prefixe "chosen_" -> c'est un mot ajouté
						// TODO : si l'auteur n'a pas le droit d'ajouter ce mot, on ne le prend pas en compte
						$valeurs_mots_nouveaux_groupe[] = substr($mot, strlen($prefixe_chosen));
					} else {
						// c'est un mot existant
						$valeurs_mots_groupe[] = $mot;
					}
				}
				// Mise à jour des variables
				// On envoie même s'il n'y a pas d'erreur, ça servira à traiter()
				set_request('groupe_'.$id_groupe, $valeurs_mots_groupe);
				set_request('nouveaux_groupe_'.$id_groupe, $valeurs_mots_nouveaux_groupe);

				// Est-ce que ces nouveaux mots existent déjà dans d'autres groupes ?
				include_spip('base/abstract_sql');
				$msg = '';
				foreach ($valeurs_mots_nouveaux_groupe as $titre){
					$titre_groupe = sql_getfetsel('g.titre',
						'spip_mots AS m LEFT JOIN spip_groupes_mots AS g ON m.id_groupe=g.id_groupe',
						"m.titre REGEXP ".sql_quote("^([0-9]+[.] )?".preg_quote(supprimer_numero($titre))."$"));
					if ($titre_groupe) {
						// Le mot existe déjà, dans un autre groupe
						$msg = $msg . _T('diogene_mots:erreur_mot_dans_autre_groupe', array('mot' => $titre, 'groupe' => $titre_groupe));
					}
				}

				if ($msg !== '') {
					// Si oui, est-ce qu'on a déjà demandé confirmation pour les nouveaux mots ?
					$valeurs_mots_nouveaux_groupe_hidden = unserialize(_request('confirm_nouveaux_groupe_'.$id_groupe));
					if(!$valeurs_mots_nouveaux_groupe_hidden
						|| array_diff($valeurs_mots_nouveaux_groupe_hidden, $valeurs_mots_nouveaux_groupe)
						|| array_diff($valeurs_mots_nouveaux_groupe, $valeurs_mots_nouveaux_groupe_hidden)) {
						// Pas de confirmation, puisqu'il y a eu un changement dans les mots nouveaux
						// On envoie donc un message d'erreur pour confirmation
						$msg = $msg . _T('diogene_mots:erreur_confirmer_creation_mots_nouveaux');
						$au_moins_une_erreur = true;
					}
					// Et dans tous les cas, on prépare un champ <input> hidden avec les nouveaux mots
					$erreurs['groupe_'.$id_groupe] = $msg . " <input type='hidden' name='confirm_nouveaux_groupe_".$id_groupe."' value='".serialize($valeurs_mots_nouveaux_groupe)."' />";
				}
			}
			// S'il y a des erreurs, on les envoie, avec tous les champs cachés
			if ($au_moins_une_erreur)
				$flux['data'] = array_merge($flux['data'], $erreurs);
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
	$pipeline = pipeline('diogene_objets');
	if (in_array($flux['args']['type'],array_keys($pipeline)) && isset($pipeline[$flux['args']['type']]['champs_sup']['mots']) AND ($id_diogene = _request('id_diogene'))) {
		include_spip('action/editer_mot');
		$invalider = false;
		$objet = $flux['args']['type'];
		$id_objet = $flux['args']['id_objet'];

		$options_complements = unserialize(sql_getfetsel("options_complements","spip_diogenes","id_diogene=".intval($id_diogene)));

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

		/**
		 * On traite chaque groupe séparément
		 * Si c'est une modification d'objet il se peut qu'il faille supprimer les anciens mots
		 * On fait une vérifications sur chaque groupe
		 */
		foreach($groupes_possibles as $id_groupe){
			$mots_multiples = array();
			$requete_id_groupe = _request('groupe_'.$id_groupe) ? _request('groupe_'.$id_groupe) : array();
			// On crée les mots nouveaux si nécessaire
			if (test_plugin_actif('chosen')) {
				$prefixe_chosen = "chosen_";
				foreach(_request('nouveaux_groupe_'.$id_groupe) as $cle => $titre){
					// TODO : si l'auteur n'a pas le droit d'ajouter ce mot, on ne le prend pas en compte
					if ($titre_propre = corriger_caracteres(trim($titre))) {
						$id_mot = mot_inserer($id_groupe);
						$c = array('titre' => $titre_propre);
						// C'est sale - TODO - utiliser plutôt mot_modifier($id_mot, $c)
						if (sql_updateq('spip_mots', $c, 'id_mot='.$id_mot)) {
							$invalider = true;
							// un fois créé, on ajoute l'identifiant pour pouvoir associer le mot ensuite
							$requete_id_groupe[] = $id_mot;
						}
					}
				}
			}

			$result = sql_allfetsel('0+mot.titre AS num, mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as liens ON mot.id_mot=liens.id_mot','liens.objet='.sql_quote($objet).' AND mot.id_groupe='.intval($id_groupe).' AND liens.id_objet='.intval($id_objet),'','num, mot.titre');
			foreach ($result as $row) {
				$mots_multiples[] = $row['id_mot'];
			}
			
			if(is_array($requete_id_groupe)){
				foreach($requete_id_groupe as $cle => $mot){
					/**
					 * Si le mot est déja dans les mots, on le supprime juste
					 * de l'array des mots originaux
					 */
					if(in_array($mot, $mots_multiples))
						$mots_multiples = array_diff($mots_multiples,array($mot));
					else{
						sql_insertq('spip_mots_liens', array('id_mot' =>$mot,  'id_objet' => $id_objet,'objet'=> $objet));
						$invalider = true;
					}
				}
			}
			else{
				if(in_array($requete_id_groupe, $mots_multiples))
					$mots_multiples = array_diff($mots_multiples,array($requete_id_groupe));
				else{
					sql_insertq('spip_mots_liens', array('id_mot' =>$requete_id_groupe,  'id_objet' => $id_objet,'objet'=> $objet));
					$invalider = true;
				}
			}
			/**
			 * S'il reste quelque chose dans les mots d'origine, on les délie de l'objet
			 */
			if(count($mots_multiples)>0){
				sql_delete('spip_mots_liens','objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_mot IN ('.implode(',',$mots_multiples).')');
				$invalider = true;
			}

			// On nettoie les variables mises à jour dans verifier()
			set_request('groupe_'.$id_groupe, $requete_id_groupe);
			set_request('nouveaux_groupe_'.$id_groupe, array());
			if($invalider){
				include_spip('inc/invalideur');
				suivre_invalideur("id='$objet/$id_objet'");
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition (SPIP)
 * On enregistre les limites de mots dans l'édition de diogènes
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_mots_pre_edition($flux){
	if($flux['args']['table'] == 'spip_diogenes'){
		$options_complements = unserialize($flux['data']['options_complements']);
		if(is_array($options_complements)){
			if(is_array(unserialize($options_complements['mots_obligatoires'])) && count(unserialize($options_complements['mots_obligatoires'])) > 0){
				foreach(unserialize($options_complements['mots_obligatoires']) as $groupe_obligatoire){
					if(count(_request('mot_selection_'.$groupe_obligatoire)) > 0){
						$options_complements['mot_selection_'.$groupe_obligatoire] = serialize(_request('mot_selection_'.$groupe_obligatoire));
					}
				}
			}
			if(is_array(unserialize($options_complements['mots_facultatifs']))  && count(unserialize($options_complements['mots_facultatifs'])) > 0){
				foreach(unserialize($options_complements['mots_facultatifs']) as $mots_facultatifs){
					if(count(_request('mot_selection_'.$mots_facultatifs)) > 0){
						$options_complements['mot_selection_'.$mots_facultatifs] = serialize(_request('mot_selection_'.$mots_facultatifs));
					}
				}
			}
			$flux['data']['options_complements'] = serialize($options_complements);
		}
	}
	return $flux;
}
/**
 * Insertion dans le pipeline diogene_objets (Diogene)
 * Fonction permettant de mettre des mots dans le formulaire d'un Diogene 
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_mots_diogene_objets($flux){
	$flux['article']['champs_sup']['mots'] = _T('diogene_mots:form_legend');
	if(defined('_DIR_PLUGIN_PAGES'))
		$flux['page']['champs_sup']['mots'] = _T('diogene_mots:form_legend');
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
	$array[] = 'mots_creer_dans_public';
	$array[] = 'montrer_titre_et_descriptif';
	return $array;
}

?>
