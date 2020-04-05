<?php
/**
 * Utilisations de pipelines par ingrédients
 *
 * @plugin     ingrédients
 * @copyright  2015
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Ingredient\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */



/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function ingredient_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les ingredients
	if (!$e['edition'] and in_array($e['type'], array('ingredient'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}


	// ingredients sur les articles
	if (!$e['edition'] and in_array($e['type'], array('article'))) {
		$texte .= recuperer_fond('prive/objets/editer/lier_ingredients', array(
			'table_source' => 'ingredients',
			'objet' => $e['type'],
			'id_article' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($flux['args']['exec'] == 'ingredient') {
		$flux['data'] .= recuperer_fond('prive/objets/liste/ingredients_articles', array(
			'id_ingredient' => $flux['args']['id_ingredient']
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function ingredient_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/ingredients', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('ingredient:info_ingredients_auteur')
		), array('ajax' => true));
	}
	return $flux;
}

/**
 * Optimiser la base de données en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function ingredient_optimiser_base_disparus($flux) {
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('ingredient'=>'*'), '*');
	return $flux;
}
