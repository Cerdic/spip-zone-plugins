<?php
/**
 * Utilisations de pipelines par Mots arborescents
 *
 * @plugin     Mots arborescents
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Motsar\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajoute la liste des sous mots et un bouton de création de
 * sous mot sur la vue d'un mot.
 *
 * @pipeline afficher_complement_objet
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline complétées
**/
function motsar_afficher_complement_objet($flux) {
	// sur la vue d'un mot
	if ($flux['args']['type'] == 'mot' and $id_mot = $flux['args']['id']) {
		// completer la vue avec les informations des mots enfants
		$id_groupe = sql_getfetsel('id_groupe', 'spip_mots', 'id_mot='. intval($id_mot));
		if ($id_groupe) {
			$conf_arborescente = sql_getfetsel('mots_arborescents', 'spip_groupes_mots', 'id_groupe=' . $id_groupe);
			if ($conf_arborescente == 'oui') {
				$flux['data'] .= recuperer_fond("prive/squelettes/contenu/inc-mots", array('id_parent' => $id_mot, 'id_groupe' => $id_groupe), "ajax");
			}
		}
	}
	return $flux;
}

/**
 * Ajoute un commentaire si un groupe de mot accepte des mots arborescents
 * sur la vue d'un groupe
 *
 * @pipeline afficher_fiche_objet
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline complétées
**/
function motsar_afficher_contenu_objet($flux) {
	// sur la vue d'un groupe de mots
	if ($flux['args']['type'] == 'groupe_mots') {
		$id_objet = $flux['args']['id_objet'];
		$mots_arborescents = sql_getfetsel('mots_arborescents', 'spip_groupes_mots', 'id_groupe=' . $id_objet);
		if ($mots_arborescents == 'oui') {
			// completer la vue avec l'information d'arborescence
			$cherche = "/(<\/div>\s*<div class=\"groupe_mots-qui\">)/is";
			if (preg_match($cherche, $flux['data'], $m)) {
				$texte = "<div class='champ contenu_mots_arborescents'>\n"
						. "\t<div class='label'>" . _T('motsar:option_autoriser_mots_arborescents') . "</div>\n"
						. "\t<div dir='" . lang_dir() . "' class='mots_arborescents'>" . _T('motsar:option_autoriser_mots_arborescents') . "</div>\n"
						. "</div>\n";
				$flux['data'] = preg_replace($cherche, $texte.'$1', $flux['data'], 1);
			}
		}
	}
	return $flux;
}


/**
 * Ajoute le parent dans l'environnement d'un nouveau mot, s'il est connu 
 *
 * @pipeline formulaire_charger
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function motsar_formulaire_charger($flux) {
	// sur le formulaire d'édition de groupe de mot
	if ($flux['args']['form'] == 'editer_mot') {
		// si c'est un nouveau mot
		if ($flux['data']['id_mot'] == 'oui') {
			// le parent est dans l'url ?
			if ($id_parent = _request('id_parent')) {
				$flux['data']['id_parent'] = $id_parent;
			}
		}
	}
	return $flux;
}


/**
 * Verifie que le parent d'un mot
 * n'est pas ce mot lui-même !
 *
 * @pipeline formulaire_verifier
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function motsar_formulaire_verifier($flux) {
	// sur le formulaire d'édition de groupe de mot
	if ($flux['args']['form'] == 'editer_mot') {
		// tester que le parent ne vaut pas le groupe
		if ($id_parent = _request('id_parent')
		and $id_mot = _request('id_mot'))
		{
			if ($id_parent == $id_mot) {
				$flux['data']['id_parent'] = _T('motsar:erreur_parent_sur_mot');
			}
			elseif (
			  include_spip('motsar_fonctions') // calcul_branche_mot_in
			  and in_array($id_parent, explode(',', calcul_branche_mot_in($id_mot))))
			{
				$flux['data']['id_parent'] = _T('motsar:erreur_parent_sur_mot_enfant');
			}
		}
	}
	return $flux;
}




/**
 * Modifie les champs du formulaire de groupe de mot et de mots
 *
 * Sur les mots :
 * - ajouter le sélecteur de parenté
 *
 * Sur les groupes :
 * - proposer l'option mots_arborescents
 * 
 * @pipeline formulaire_fond
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function motsar_formulaire_fond($flux) {
	if (!in_array($flux['args']['form'], array('editer_groupe_mot', 'editer_mot'))) {
		return $flux;
	}

	$env = $flux['args']['contexte'];

	// sur le formulaire d'édition de mot
	if ($flux['args']['form'] == 'editer_mot') {
		// la parenté sur tous : on récupère le sélecteur et on l'ajoute après le titre...
		$selecteur_parent = recuperer_fond('formulaires/selecteur_mot_parent', $env);

		$cherche = "/(<(li|div)[^>]*class=(?:'|\")editer editer_titre.*?<\/\\2>)\s*(<(li|div)[^>]*class=(?:'|\")editer)/is";
		if (preg_match($cherche, $flux['data'], $m)) {
			$flux['data'] = preg_replace($cherche, '$1'.$selecteur_parent.'$3', $flux['data'], 1);
		}
	}

	// sur le formulaire d'édition de mot
	elseif ($flux['args']['form'] == 'editer_groupe_mot') {
		// l'option oui/non technique pour autoriser les mots_arborescents
		$option_mots_arborescents = recuperer_fond('formulaires/option_mots_arborescents', $env);

		$cherche = "/(<(li|div)[^>]*class=(?:'|\")editer editer_groupe_mots_reglage_avance.*?<\/\\2>)\s*(<(li|div)[^>]*class=(?:'|\")editer)/is";
		if (preg_match($cherche, $flux['data'], $m)) {
			$flux['data'] = preg_replace($cherche, '$1'.$option_mots_arborescents.'$3', $flux['data'], 1);
		}
	}

	return $flux;
}

/**
 * Modifie les champs du formulaire de groupe de mot et de mots
 *
 * Sur les mots :
 * - ajouter le sélecteur de parenté
 *
 * Sur les groupes :
 * - proposer l'option mots_arborescents
 *
 * @pipeline formulaire_fond
 * @note
 *     Code utilisant querypath (mais non fonctionnel avec libxml version 2.9.2 :/)
 * 
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function motsar_formulaire_fond_avec_querypath($flux) {
	if (!in_array($flux['args']['form'], array('editer_groupe_mot', 'editer_mot'))) {
		return $flux;
	}

	$html = $flux['data'];
	$env = $flux['args']['contexte'];

	// charger QueryPath
	include_spip('inc/querypath');
	$qp = spip_query_path($html, 'body');


	// sur le formulaire d'édition de mot
	if ($flux['args']['form'] == 'editer_mot') {
		// la parenté sur tous
		// on récupère le sélecteur et on l'ajoute après le titre...
		$selecteur_parent = recuperer_fond('formulaires/selecteur_mot_parent', $env);
		$qp->top('body')->find('.editer_titre')->after($selecteur_parent);
	}

	// sur le formulaire d'édition de mot
	elseif ($flux['args']['form'] == 'editer_groupe_mot') {
		// l'option oui/non technique pour autoriser les mots_arborescents
		$option_mots_arborescents = recuperer_fond('formulaires/option_mots_arborescents', $env);
		$qp->top('body')->find('.fieldset_config')->append($option_mots_arborescents);
	}

	// retourner le HTML modifie
	$flux['data'] = $qp->top('body>div')->xhtml();

	return $flux;
}


/**
 * Insère des modifications juste avant la création d'un mot
 * 
 * Lors de la création d'un mot :
 * - Ajoute l'id_mot_racine et l'id_parent
 *
 * Lors de la création d'un groupe de mot :
 * - Ajoute l'option mots_arborescents
 *
 * @pipeline pre_insertion
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline complétées
**/
function motsar_pre_insertion($flux) {
	// lors de la création d'un mot
	if ($flux['args']['table'] == 'spip_mots') {
		if ($id_parent = _request('id_parent')) {
			$id_racine = sql_getfetsel('id_mot_racine', 'spip_mots', 'id_mot=' . sql_quote($id_parent));
			// si et seulement si le parent demandé existe
			if ($id_racine) {
				$flux['data']['id_parent'] = $id_parent;
				$flux['data']['id_mot_racine'] = $id_racine;
			}
		}
	}
	// lors de la création d'un groupe de mot
	if ($flux['args']['table'] == 'spip_groupes_mots') {
		if ($mots_arborescents = _request('mots_arborescents')) {
			$flux['data']['mots_arborescents'] = $mots_arborescents;
		}
	}
	return $flux;
}



/**
 * Insère des modifications juste après de la création d'un mot
 * 
 * Lors de la création d'un mot :
 * - Ajoute l'id_mot_racine si le mot est à la racine
 *
 * @pipeline post_insertion
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline complétées
**/
function motsar_post_insertion($flux) {
	// lors de la création d'un groupe
	if ($flux['args']['table'] == 'spip_mots') {
		$id_mot = $flux['args']['id_objet'];
		// si le mot est à la racine,
		// c'est a dire que 'id_mot_racine' n'est pas défini ou nul
		// c'est que nous avons créé un mot racine. Il faut mettre
		// id_mot_racine sur id_mot, maintenant qu'on le connait. 
		if (empty($flux['data']['id_mot_racine'])) {
			sql_updateq('spip_mots', array('id_mot_racine' => $id_mot), 'id_mot=' . sql_quote($id_mot));
		}
	}
	return $flux;
}


/**
 * Insère des modifications lors de l'édition de mots
 * 
 * Lors de l'édition d'un mot :
 * - Modifie l'id_parent choisi et définit l'id_mot_racine et la profondeur
 * 
 * Lors de l'édition d'un groupe de mot :
 * - Prend en compte l'option mots_arborescents
 *
 * @pipeline pre_edition
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline complétées
**/
function motsar_pre_edition($flux) {
	// lors de l'édition d'un mot
	$table = 'spip_mots';
	if ($flux['args']['table'] == $table
	and $flux['args']['action'] == 'modifier')
	{
		$id_mot = $flux['args']['id_objet'];
		$id_parent_ancien  = sql_getfetsel('id_parent', $table, 'id_mot=' . sql_quote($id_mot));
		$id_parent_nouveau = _request('id_parent');
		// uniquement s'ils sont différents
		if ($id_parent_ancien != $id_parent_nouveau
		// que le nouveau parent n'est pas notre groupe !
		and $id_mot != $id_parent_nouveau
		// et que le groupe parent n'est pas un de nos enfants
		and include_spip('motsar_fonctions') // calcul_branche_mot_in
		and !in_array($id_parent_nouveau, explode(',', calcul_branche_mot_in($id_mot)))
		) {
			$id_racine = '';
			$profondeur = 0;
			// soit c'est la racine
			if (!$id_parent_nouveau) {
				// auquel cas l'identifiant racine est le meme que notre mot, qui migre à la racine
				$id_racine = $id_mot;
			// soit le groupe existe
			} else {
				$parent = sql_fetsel(array('profondeur', 'id_mot_racine'), $table, 'id_mot=' . sql_quote($id_parent_nouveau));
				if ($parent) {
					$id_racine  = $parent['id_mot_racine'];
					$profondeur = $parent['profondeur'] + 1;
				}
			}
			if ($id_racine) {
				$flux['data']['id_parent']     = $id_parent_nouveau;
				$flux['data']['id_mot_racine'] = $id_racine;
				$flux['data']['profondeur']    = $profondeur;
				// pour le pipeline de post_edition. Permet entre autre de savoir
				// qu'il faudra actualiser les mots de la branche
				set_request('motsar_definir_heritages', true);
			}
		}
	}

	// lors de l'édition d'un groupe de mot
	if ($flux['args']['table'] == 'spip_groupes_mots'
	and $flux['args']['action'] == 'modifier')
	{
		if ($mots_arborescents = _request('mots_arborescents')) {
			$flux['data']['mots_arborescents'] = $mots_arborescents;
		}
	}
	return $flux;
}

/**
 * Modifie les données héritées d'un mot
 * 
 * Modifie les héritages lorsqu'un parent change ou lorsqu'on modifie
 * un mot racine qui a pu changer des données
 *
 * Modifie les mots si la configuration d'un groupe est modifiée : on les applatit.
 *
 * @pipeline post_edition
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline complétées
**/
function motsar_post_edition($flux) {
	// lors de l'édition d'un mot
	if ($flux['args']['table']  == 'spip_mots' and $flux['args']['action'] == 'modifier'
		// soit le parent a change, soit le groupe racine est modifie
		and (_request('motsar_definir_heritages') OR empty($flux['data']['id_parent'])))
	{
		$id_mot = $flux['args']['id_objet'];
		include_spip('motsar_fonctions');
		motsar_definir_heritages_mot($id_mot);
		propager_les_mots_arborescents();
	}

	// lors de l'édition d'un groupe de mot
	elseif ($flux['args']['table']  == 'spip_groupes_mots' and $flux['args']['action'] == 'modifier'
		// soit le parent a change, soit le groupe racine est modifie
		and ($mots_arborescents = _request('mots_arborescents')))
	{
		$id_groupe = $flux['args']['id_objet'];
		include_spip('motsar_fonctions');
		motsar_definir_heritages($id_groupe);
	}

	return $flux;
}
