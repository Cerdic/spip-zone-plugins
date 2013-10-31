<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline diogene_ajouter_saisies (Diogene)
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux le contexte modifié passé aux suivants
 */
function diogene_mots_diogene_ajouter_saisies($flux){
	$objet = $flux['args']['type'];
	$id_table_objet = id_table_objet($flux['args']['type']);
	$id_objet = $flux['args']['contexte'][$id_table_objet];
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
		
		if (intval($id_objet)) {				
			//On récupère les mots qui sont peut être associés
			foreach($groupes_possibles as $id_groupe){
				if(in_array($id_groupe,$mots_obligatoires)){
					$valeurs_mots['groupe_obligatoire_'.$id_groupe] = 'oui';
				}
				if (_request('groupe_'.$id_groupe)) {
					// Pour récupérer la selection courante en cas d'erreur dans vérifier() ou traiter()
					$valeurs_mots['groupe_'.$id_groupe] = _request('groupe_'.$id_groupe);
				} else if (sql_getfetsel('unseul','spip_groupes_mots','id_groupe='.intval($id_groupe))== 'oui') {
					$valeurs_mots['groupe_'.$id_groupe] = sql_fetsel('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as mots_liens ON (mot.id_mot=mots_liens.id_mot)','mots_liens.objet='.sql_quote($objet).' AND mots_liens.id_objet='.intval($id_objet).' AND mot.id_groupe='.intval($id_groupe));
				}else {
					$result = sql_select('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as mots_liens ON mot.id_mot=mots_liens.id_mot','mots_liens.objet='.sql_quote($objet).' AND mot.id_groupe='.intval($id_groupe).' AND mots_liens.id_objet='.intval($id_objet));
					while ($row = sql_fetch($result)) {
						$valeurs_mots['groupe_'.$id_groupe][] = $row['id_mot'];
					}
				}
				if (_request('nouveaux_groupe_'.$id_groupe)) {
					// Pour récupérer les nouveaux mots en cas d'erreur dans vérifier() ou traiter()
					$valeurs_mots['nouveaux_groupe_'.$id_groupe] = _request('nouveaux_groupe_'.$id_groupe);
				}
			}
		}
		
		if (is_array($valeurs_mots)) {
			$flux['args']['contexte'] = array_merge($flux['args']['contexte'],$valeurs_mots);
		}

		/* Paramètre pour permettre de créer des nouveaux mots dans les groupes choisis */
		/* TODO : seulement si l'auteur a le droit d'ajouter de nouveaux mots (peut être différent pour chaque groupe de mots ?) */
		/* TODO : parametre par groupe au lieu d'un parametre general */
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
		$diogene = sql_fetsel("*","spip_diogenes","id_diogene=".intval($id_diogene));
		$options_complements = unserialize($diogene['options_complements']);
		$erreurs = $flux['args']['erreurs'];
		// On teste si les groupes obligatoires sont ok
		if (isset($options_complements['mots_obligatoires']) && is_array(unserialize($options_complements['mots_obligatoires']))){
			foreach(unserialize($options_complements['mots_obligatoires']) as $groupe_obligatoire=>$id_groupe){
				$mots_groupe = _request('groupe_'.$id_groupe);
				if(empty($mots_groupe) OR is_null($mots_groupe) OR !is_numeric($mots_groupe)){
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
						$valeurs_mots_nouveaux_groupe[] = substr($mot, strlen($prefixe_chosen));
					} else {
						// c'est un mot existant
						$valeurs_mots_groupe[] = $mot;
					}
				}
				// Mise à jour des variables
				// TODO - verifier si on doit vraiment envoyer tout le temps, ou seulement en cas d'erreur
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
			if ($au_moins_une_erreur) {
				$flux['data'] = array_merge($flux['data'], $erreurs);
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
	// -> créer les mots dans la base.
	// -> gérer les erreurs dans traiter() ? je ne crois pas qu'il puisse en avoir
	$pipeline = pipeline('diogene_objets');
	if (in_array($flux['args']['type'],array_keys($pipeline)) && isset($pipeline[$flux['args']['type']]['champs_sup']['mots']) AND ($id_diogene = _request('id_diogene'))) {
		$id_objet = $flux['args']['id_objet'];

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

		/**
		 * On traite chaque groupe séparément
		 * Si c'est une modification d'objet il se peut qu'il faille supprimer les anciens mots
		 * On fait une vérifications sur chaque groupe
		 */
		foreach($groupes_possibles as $id_groupe){
			$mots_multiples = array();
			$requete_id_groupe = is_array(_request('groupe_'.$id_groupe)) ? _request('groupe_'.$id_groupe) : array('cle' => _request('groupe_'.$id_groupe));

			// On crée les mots nouveaux si nécessaire
			if (test_plugin_actif('chosen')) {
				include_spip('action/editer_mot');
				$prefixe_chosen = "chosen_";
				foreach($requete_id_groupe as $cle => $mot){
					if (substr($mot, 0, strlen($prefixe_chosen)) == $prefixe_chosen) {
						// prefixe "chosen_" -> c'est un nouveau mot, on le crée
						$titre = substr($mot, strlen($prefixe_chosen));
						$id_mot = mot_inserer($id_groupe);
						$c = array('titre' => $titre);
						mot_modifier($id_mot, $c);
						// on remplace le titre par le nouvel identifiant pour pouvoir associer le mot ensuite
						$requete_id_groupe[$cle] = $id_mot;
					}
				}
			}

			/**
			 * Si le select est multiple
			 */
			if(count($requete_id_groupe) > 1){
				$result = sql_select('0+mot.titre AS num, mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as liens ON mot.id_mot=liens.id_mot','liens.objet="'.$flux['args']['type'].'" AND id_groupe='.intval($id_groupe).' AND liens.id_objet='.intval($id_objet),'','num, mot.titre');
				while ($row = sql_fetch($result)) {
					$mots_multiples[] = $row['id_mot'];
				}
				foreach($requete_id_groupe as $cle => $mot){
					/**
					 * Si le mot est déja dans les mots, on le supprime juste
					 * de l'array des mots originaux
					 */
					if(in_array($mot, $mots_multiples)){
						unset($mots_multiples[$cle]);
					}
					else{
						sql_insertq('spip_mots_liens', array('id_mot' =>$mot,  'id_objet' => $id_objet,'objet'=> $flux['args']['type']));
					}
				}
			}
			/**
			 * Si le select est simple
			 */
			else{
				$id_mot = array_pop($requete_id_groupe);
				if(!is_array($mots_uniques = sql_fetsel('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as liens ON (mot.id_mot=liens.id_mot)','liens.objet="'.$flux['args']['type'].'" AND liens.id_objet='.intval($id_objet).' AND mot.id_groupe='.intval($id_groupe))))
					$mots_uniques = array();
				if(in_array($id_mot, $mots_uniques)){
					unset($mots_uniques);
				}
				else{
					sql_insertq('spip_mots_liens', array('id_mot' => $id_mot,  'id_objet' => $id_objet,'objet'=>$flux['args']['type']));
				}
			}
			/**
			 * S'il reste quelque chose dans les mots d'origine, on les délie de l'objet
			 */
			if(count($mots_uniques)>0){
				sql_delete('spip_mots_liens','objet="'.$flux['args']['type'].'" AND id_objet='.intval($id_objet).' AND id_mot IN ('.implode(',',$mots_uniques).')');
			}
			if(count($mots_multiples)>0){
				sql_delete('spip_mots_liens','objet="'.$flux['args']['type'].'" AND id_objet='.intval($id_objet).' AND id_mot IN ('.implode(',',$mots_multiples).')');
			}
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
	return $array;
}

?>
