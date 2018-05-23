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
			)
		);
		
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $enfants, $p, 0);
		} else {
			$flux['data'] .= $enfants;
		}
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
				'arbo' => 'oui',
				'simple' => 'oui',
				'titre' => _T('chapitre:titre_plan'),
			)
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
		if ($id_parent = intval($flux['data']['id_parent']) or $id_parent = intval($flux['args']['id_parent'])) {
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
 * Agir après l'enregistrement des données lors de l'édition d'un contenu
 *
 * => Quand on dépublie un chapitre, dépublier aussi tous ses enfants (la branche)
 *
 * @pipeline pre_insertion
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function chapitres_post_edition($flux) {

	// Si on institue un chapitre
	if ($flux['args']['action'] == 'instituer'
		and $flux['args']['table'] == 'spip_chapitres'
		and $statut = $flux['data']['statut']
		and $statut_ancien = $flux['args']['statut_ancien']
		and $id_chapitre = intval($flux['args']['id_objet'])
	) {

		// Récupérer les statuts de publication
		include_spip('base/objets');
		$info_statut = array_shift(objet_info('chapitre', 'statut'));
		$statuts_publie = explode(',', $info_statut['publie']);

		// Si on dépublie, poser le même statut à toute la branche
		if (in_array($statut_ancien, $statuts_publie)
			and !in_array($statut, $statuts_publie)
			and count($ids_branche = array_map('intval', explode(',', calcul_branche_in_chapitres($id_chapitre))))
		) {
			// Enlever le chapitre parent, qui a déjà été institué
			if (($k = array_search($id_chapitre, $ids_branche)) !== false) {
				unset($ids_branche[$k]);
			}
			// Dépublier
			sql_updateq(
				'spip_chapitres',
				array('statut' => $statut),
				sql_in('id_chapitre', $ids_branche)
			);
		}

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
