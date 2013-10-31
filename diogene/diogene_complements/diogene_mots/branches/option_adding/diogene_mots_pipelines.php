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
			foreach($groupes_possibles as $groupe){
				if(in_array($groupe,$mots_obligatoires)){
					$valeurs_mots['groupe_obligatoire_'.$groupe] = 'oui';
				}
				if (sql_getfetsel('unseul','spip_groupes_mots','id_groupe='.intval($groupe))== 'oui') {
					$valeurs_mots['groupe_'.$groupe] = sql_fetsel('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as mots_liens ON (mot.id_mot=mots_liens.id_mot)','mots_liens.objet='.sql_quote($objet).' AND mots_liens.id_objet='.intval($id_objet).' AND mot.id_groupe='.intval($groupe));
				}else {
					$result = sql_select('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as mots_liens ON mot.id_mot=mots_liens.id_mot','mots_liens.objet='.sql_quote($objet).' AND mot.id_groupe='.intval($groupe).' AND mots_liens.id_objet='.intval($id_objet));
					while ($row = sql_fetch($result)) {
						$valeurs_mots['groupe_'.$groupe][] = $row['id_mot'];
					}
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

		/* TODO : vérifier 
		* si des nouveaux mots ont été proposées (avec mots_creer_dans_public), pour chacun :
		*
		* 1./ si un mot avec le même titre existe déjà dans un autre groupe (ie. création impossible)
		* 
		* 1a./ si possible (ie. si cet autre groupe est dans les groupes de mots du diogene) :
		* - mettre "selected" sur ce mot dans l'autre groupe
		* - message en "warning" pour indiquer le changement
		* - et retour sur le formulaire pour obtenir la validation de l'utilisateur (message_erreur + editable)
		* 1b./ sinon :
		* - erreur "impossible de créer le mot *** : ce mot existe déjà dans le groupe *** - contacter l'administrateur"
		* - supprimer l'<option> correspondante
		* - retour sur le formulaire (message_erreur + editable)
		* 
		*/
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

		// Le préfixe ajouté par chosen sur les nouveaux mots proposés
		$prefixe_chosen = lire_config('chosen/prefixe_create_option');
		// S'il n'y a pas de préfixe, ou que le préfixe est un nombre, on ne peut pas 
		// différencier les nouveaux mots des index "id_mot". On fera au mieux.
		$prefixe_chosen_ok = ($prefixe_chosen && !is_numeric($prefixe_chosen));

		/**
		 * On traite chaque groupe séparément
		 */
		$mots_nouveaux = array();
		foreach($groupes_possibles as $id_groupe){
			$mots_nouveaux_groupe = array();
			// Trouver les nouveaux mots proposés
			if (is_array(_request('groupe_'.$id_groupe))){
				foreach(_request('groupe_'.$id_groupe) as $cle => $mot){
					if ($prefixe_chosen_ok) {
						// le préfixe est une chaine de caractères, on la retire quand elle existe
						if (substr($mot, 0, strlen($prefixe_chosen)) == $prefixe_chosen) {
							$mots_nouveaux_groupe[] = substr($mot, strlen($prefixe_chosen));
						}
					} else if (!is_numeric($mot)) {
						// le préfixe n'existe pas ou est un entier, on le retire quand le mot n'est pas lui même un entier
						$mots_nouveaux_groupe[] = substr($mot, strlen($prefixe_chosen));
					}
					// sinon: le mot est soit un index (mot existant), soit un nouveau mot du type "123". Tant pis, dans ce dernier cas, on ne le prend pas en compte.
				}
			}
			// TODO : On cherche si ces nouveaux mots existent déjà dans d'autres groupes
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
	 /* 2./ créer les nouveaux mots dans le groupe de mots 
	 * 
	 * en cas d'erreur :
	 * - les mots ***, ***, ... n'ont pas pu être créés - contacter l'administratrice (message_erreur + editable)
	 *
	 * à la fin de la boucle
	 * - si erreur, arrêt du traitement, et retour sur le formulaire avec les messages d'erreur.
	 *   - note : pas besoin de recréer artificiellement les <option>, puisque soit les mots auront été créés (en cas de réussite), soit ils doivent être retirés (et message d'erreur)
	 *   - note2 : il faut par contre s'assurer que le paramètre "selected" des <option> sont bien positionnés (valeur des "groupe_ID" à renvoyer)
	 * - sinon, aucun erreur, on passe au traitement pour associer les mots clés à l'article
*/
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
		foreach($groupes_possibles as $groupe){
			$mots_multiples = array();

			/**
			 * Si le select est multiple
			 */
			if(is_array(_request('groupe_'.$groupe))){
				$result = sql_select('0+mot.titre AS num, mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as liens ON mot.id_mot=liens.id_mot','liens.objet="'.$flux['args']['type'].'" AND id_groupe='.intval($groupe).' AND liens.id_objet='.intval($id_objet),'','num, mot.titre');
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
						sql_insertq('spip_mots_liens', array('id_mot' =>$mot,  'id_objet' => $id_objet,'objet'=> $flux['args']['type']));
					}
				}
			}
			/**
			 * Si le select est simple
			 */
			else{
				if(!is_array($mots_uniques = sql_fetsel('mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as liens ON (mot.id_mot=liens.id_mot)','liens.objet="'.$flux['args']['type'].'" AND liens.id_objet='.intval($id_objet).' AND mot.id_groupe='.intval($groupe))))
					$mots_uniques = array();
				if(in_array(_request('groupe_'.$groupe), $mots_uniques)){
					unset($mots_uniques);
				}
				else{
					sql_insertq('spip_mots_liens', array('id_mot' =>_request('groupe_'.$groupe),  'id_objet' => $id_objet,'objet'=>$flux['args']['type']));
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
