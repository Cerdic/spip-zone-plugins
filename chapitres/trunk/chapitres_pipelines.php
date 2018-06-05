<?php
/**
 * Utilisations de pipelines par Chapitres
 *
 * @plugin     Chapitres
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Chapitres\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function chapitres_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les chapitres
	if (!$e['edition'] and in_array($e['type'], array('chapitre'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

/**
 * Ajout de contenu sous la fiche d'un objet
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function chapitres_affiche_enfants($flux) {
	include_spip('inc/config');
	$objets = lire_config('chapitres/objets', array());
	
	if (
		isset($flux['args']['objet'])
		and isset($flux['args']['id_objet'])
		and $objet = $flux['args']['objet']
		and $id_objet = intval($flux['args']['id_objet'])
		and in_array(table_objet_sql($objet), $objets)
	) {
		$enfants = recuperer_fond(
			'prive/objets/contenu/chapitre-enfants',
			array(
				'objet' => $objet,
				'id_objet' => $id_objet,
				'chapitres' => _request('chapitres'),
				'id_chapitre' => _request('id_chapitre'),
			),
			array (
				'ajax' => true,
			)
		);
		
		$flux['data'] .= $enfants;
	}
	
	return $flux;
}

/**
 * Ajout du plan des chapitres
 *
 * @pipeline affiche_gauche
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function chapitres_afficher_config_objet($flux) {
	if ($flux['args']['type'] == 'chapitre' and $id_chapitre = intval($flux['args']['id'])) {
		$chapitre = sql_fetsel('objet, id_objet', 'spip_chapitres', 'id_chapitre = '.$id_chapitre);
		
		$plan = recuperer_fond(
			'prive/objets/liste/chapitres',
			array(
				'objet' => $chapitre['objet'],
				'id_objet' => $chapitre['id_objet'],
				'id_parent' => 0,
				'id_chapitre' => $flux['args']['id'],
				'arbo' => 'oui',
				'simple' => 'oui',
				'titre' => _T('chapitre:titre_plan'),
			),
			array('ajax' => true)
		);
		
		$flux['data'] .= $plan;
	}
	
	return $flux;
}

/**
 * Agir avant l'insertion d'un nouvel objet dans la base
 *
 * => Chapitre : définir le parent
 * => Chapitre : publier d'office éventuellement
 * 
 * @pipeline pre_insertion
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function chapitres_pre_insertion($flux) {

	if ($flux['args']['table'] == 'spip_chapitres') {

		include_spip('inc/config');

		// 1) Définir le parent
		// S'il y a un id_parent
		if ($id_parent = intval($flux['data']['id_parent'])
			or $id_parent = intval($flux['args']['id_parent'])
		) {
			$flux['data']['id_parent'] = $id_parent;
			
			// Et dans ce cas, le nouveau chapitre utilise forcément l'objet et id_objet du parent
			$parent = sql_fetsel('objet, id_objet', 'spip_chapitres', 'id_chapitre = '.intval($id_parent));
			$flux['data']['objet'] = $parent['objet'];
			$flux['data']['id_objet'] = intval($parent['id_objet']);
		}
		// Sinon il y a peut-être l'objet parent à remplir quand même
		elseif ($objet = _request('objet') and $id_objet = intval(_request('id_objet'))) {
			$flux['data']['objet'] = $objet;
			$flux['data']['id_objet'] = $id_objet;
		}

		// 2) Publier éventuellement
		if (lire_config('chapitres/publier_auto')) {
			$flux['data']['statut'] = 'publie';
		}

	}
	
	return $flux;
}


/**
 * Agir avant l'édition d'un objet
 *
 * => Modification / institution d'un chapitre : si id_parent a été modifié, le renvoyer dans la liste des champs sinon il est ignoré.
 * l'API cherche par défaut une rubrique comme parent, qui forcément n'existe pas.
 *
 * @pipeline pre_edition
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function chapitres_pre_edition($flux) {

	$zid_parent_ancien = sql_getfetsel('id_parent', 'spip_chapitres', 'id_chapitre='.intval($flux['args']['id_objet']));
	$zid_parent = _request('id_parent');

	// Si on modifie ou institue un chapitre et qu'un nouveau id_parent est sélectionné
	if (
		$flux['args']['type'] == 'chapitre'
		and in_array($flux['args']['action'], array('instituer', 'modifier'))
		and $id_chapitre = intval($flux['args']['id_objet'])
		and !is_null($id_parent = _request('id_parent'))
		and (($id_parent_ancien = sql_getfetsel('id_parent', 'spip_chapitres', 'id_chapitre='.intval($id_chapitre))) !== false)
		and $id_parent != $id_parent_ancien
	) {

		$flux['data']['id_parent'] = intval($id_parent);

	}

	return $flux;
}


/**
 * Optimiser la base de données
 *
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function chapitres_optimiser_base_disparus($flux) {

	sql_delete('spip_chapitres', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}
