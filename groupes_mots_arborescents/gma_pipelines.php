<?php
/**
 * Plugin Groupes arborescents de mots clés
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajoute la liste des sous groupes et un bouton de création de
 * sous groupe sur la vue d'un groupe. 
 *
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function gma_afficher_complement_objet($flux) {
	// sur la vue d'un groupe de mot
	if ($flux['args']['type'] == 'groupemots') {
		$id = $flux['args']['id'];
		// completer la vue avec les informations des sous groupes
		$flux['data'] .= recuperer_fond(
			"prive/squelettes/contenu/inc-groupes_mots", array('id_parent' => $id), "ajax"
		);
	}
	return $flux;
}


/**
 * Ajoute le parent dans l'environnement d'un nouveau groupe de mot
 * s'il est connu 
 *
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function gma_formulaire_charger($flux) {
	// sur le formulaire d'édition de groupe de mot
	if ($flux['args']['form'] == 'editer_groupe_mot') {
		// si c'est un nouveau groupe
		if ($flux['data']['id_groupe'] == 'oui') {
			// le parent est dans l'url ?
			if ($id_parent = _request('id_parent')) {
				$flux['data']['id_parent'] = $id_parent;
			}
		}
	}
	return $flux;
}


/**
 * Verifie que le parent d'un groupe de mot
 * n'est pas ce groupe lui-même !
 *
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function gma_formulaire_verifier($flux) {
	// sur le formulaire d'édition de groupe de mot
	if ($flux['args']['form'] == 'editer_groupe_mot') {
		// tester que le parent ne vaut pas le groupe
		if ($id_parent = _request('id_parent')
		and $id_groupe = _request('id_groupe'))
		{
			if ($id_parent == $id_groupe) {
				$flux['data']['id_parent'] = _T('gma:erreur_parent_sur_groupe');
			}
			elseif (
			  include_spip('gma_fonctions') // calcul_branche_groupe_in
			  and in_array($id_parent, explode(',', calcul_branche_groupe_in($id_groupe))))
			{
				$flux['data']['id_parent'] = _T('gma:erreur_parent_sur_groupe_enfant');
			}
		}
	}
	return $flux;
}


/**
 * Modifie les champs du formulaire de groupe de mot
 * pour :
 * - ajouter le sélecteur de parenté 
 * - n'afficher les options techniques que sur la racine
 *
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function gma_formulaire_fond($flux) {
	// sur le formulaire d'édition de groupe de mot
	if ($flux['args']['form'] == 'editer_groupe_mot') {


		$html = $flux['data'];
		$env = $flux['args']['contexte'];
		
		// charger QueryPath
		include_spip('inc/querypath');
		$qp = spip_query_path($html, 'body');

		// la parenté sur tous
		// on récupère le sélecteur et on l'ajoute après le titre...
		$selecteur_parent = recuperer_fond('formulaires/selecteur_groupe_parent', $env);
		$qp->top('body')->find('li.editer_titre')->after($selecteur_parent);

		// les paramètres techniques sont uniquement sur les groupes racine
		if ($env['id_parent']) {
			$qp->top('body')->find('li.fieldset_config')->remove();
		}

		// retourner le HTML modifie
		$flux['data'] = $qp->top('body>div')->xhtml();
	}
	return $flux;
}


/**
 * Insère des modifications lors de la création de groupes et de mots
 * 
 * Lors de la création d'un groupe de mot :
 * - Ajoute l'id_groupe_racine et l'id_parent
 *
 * Lors de la création d'un mot
 * - Définit l'id_groupe_racine
 * 
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function gma_pre_insertion($flux) {
	// lors de la création d'un groupe
	if ($flux['args']['table'] == 'spip_groupes_mots')
	{
		if ($id_parent = _request('id_parent')) {
			$id_racine = sql_getfetsel('id_groupe_racine', 'spip_groupes_mots', 'id_groupe=' . sql_quote($id_parent));
			// si et seulement si le parent demandé existe
			if ($id_racine) {
				$flux['data']['id_parent'] = $id_parent;
				$flux['data']['id_groupe_racine'] = $id_racine;
			}
		}
	}

	// lors de la création d'un mot
	if ($flux['args']['table'] == 'spip_mots')
	{
		// on récupère la racine et on l'ajoute
		$id_groupe = $flux['data']['id_groupe'];
		$id_racine = sql_getfetsel('id_groupe_racine', 'spip_groupes_mots', 'id_groupe=' . sql_quote($id_groupe));
		$flux['data']['id_groupe_racine'] = $id_racine;
	}
	return $flux;
}



/**
 * Insère des modifications lors de la création de groupes et de mots
 * 
 * Lors de la création d'un groupe de mot :
 * - Ajoute l'id_groupe_racine si le groupe est à la racine
 *
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function gma_post_insertion($flux) {
	// lors de la création d'un groupe
	if ($flux['args']['table'] == 'spip_groupes_mots')
	{
		$id_groupe = $flux['args']['id_objet'];
		// si le groupe est à la racine,
		// c'est a dire que 'id_groupe_racine' n'est pas défini ou nul
		// c'est que nous avons créé un groupe racine. Il faut mettre
		// id_groupe_racine sur id_groupe, maintenant qu'on le connait. 
		if (!isset($flux['data']['id_groupe_racine']) OR !$flux['data']['id_groupe_racine']) {
			sql_updateq(
				'spip_groupes_mots',
				array('id_groupe_racine' => $id_groupe),
				'id_groupe=' . sql_quote($id_groupe));
		}
	}
	return $flux;
}


/**
 * Insère des modifications lors de l'édition des groupes ou des mots
 * 
 * Lors de l'édition d'un groupe de mot :
 * - Modifie l'id_parent choisi et définit l'id_groupe_racine
 *
 * Lors de l'édition d'un mot
 * - Définit l'id_groupe_racine
 * 
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function gma_pre_edition($flux) {
	// lors de l'édition d'un groupe
	$table = 'spip_groupes_mots';
	if ($flux['args']['table'] == $table
	and $flux['args']['action'] == 'modifier')
	{
		$id_groupe = $flux['args']['id_objet'];
		$id_parent_ancien  = sql_getfetsel('id_parent', $table, 'id_groupe=' . sql_quote($id_groupe));
		$id_parent_nouveau = _request('id_parent');
		// uniquement s'ils sont différents
		if ($id_parent_ancien != $id_parent_nouveau
		// que le nouveau parent n'est pas notre groupe !
		and $id_groupe != $id_parent_nouveau
		// et que le groupe parent n'est pas un de nos enfants
		and include_spip('gma_fonctions') // calcul_branche_groupe_in
		and !in_array($id_parent_nouveau, explode(',', calcul_branche_groupe_in($id_groupe)))
		) {
			$id_racine = '';
			// soit c'est la racine
			if (!$id_parent_nouveau) {
				// auquel cas l'identifiant racine est le meme que notre groupe, qui migre à la racine
				$id_racine = $id_groupe;
			// soit le groupe existe
			} else {
				$id_racine = sql_getfetsel('id_groupe_racine', $table, 'id_groupe=' . sql_quote($id_parent_nouveau));
			}
			if ($id_racine) {
				$flux['data']['id_parent']        = $id_parent_nouveau;
				$flux['data']['id_groupe_racine'] = $id_racine;
				// pour le pipeline de post_edition. Permet entre autre de savoir
				// qu'il faudra actualiser les mots de la branche
				set_request('gma_definir_heritages', true);
			}
		}
	}

	// lors de l'édition d'un mot
	$table = 'spip_mots';
	if ($flux['args']['table'] == $table
	and $flux['args']['action'] == 'instituer')
	{
		$id_mot = $flux['args']['id_objet'];
		// on récupère le nouveau groupe (et l'ancien)
		$id_groupe_nouveau = $flux['data']['id_groupe'];
		$id_groupe_ancien  = sql_getfetsel('id_groupe', $table, 'id_mot=' . sql_quote($id_mot));
		// s'il a changé, on insère la nouvelle racine dans le mot
		if ($id_groupe_nouveau != $id_groupe_ancien) {
			$id_racine = sql_getfetsel('id_groupe_racine', 'spip_groupes_mots', 'id_groupe=' . sql_quote($id_groupe_nouveau));
			$flux['data']['id_groupe_racine'] = $id_racine;
		}
	}
	return $flux;
}

/**
 * Modifie les données héritées d'un groupe de mot
 * 
 * Modifie les héritages lorsqu'un parent change ou lorsqu'on modifie
 * un groupe racine qui a pu changer des paramètres de config
 * 
 * @param array $flux
 * 		Données du pipeline
 * @return array
 * 		Données du pipeline complétées
**/
function gma_post_edition($flux) {
	// lors de l'édition d'un groupe
	$table = 'spip_groupes_mots';
	if ($flux['args']['table']  == $table
	and $flux['args']['action'] == 'modifier'
	// soit le parent a change, soit le groupe racine est modifie
	and (_request('gma_definir_heritages')
	    OR !isset($flux['data']['id_parent'])
	    OR !$flux['data']['id_parent']))
	{
		$id_groupe = $flux['args']['id_objet'];
		include_spip('gma_fonctions');
		// ne mettre à jour les mots que lorsque le parent a change
		$update_mots = (bool) _request('gma_definir_heritages');
		gma_definir_heritages($id_groupe, null, $update_mots);
	}
	return $flux;
}

?>
