<?php
/**
 * Plugin Projets
 *
 * @plugin  Projets
 * @license GPL (c) 2009-2017
 * @author  Cyril Marion, Matthieu Marcillaud, RastaPopoulos
 *
 * @package SPIP\Projets\Pipelines
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/utils');

/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 */
function projets_affiche_milieu($flux) {
	include_spip('inc/pipelines_ecrire');
	include_spip('inc/config');
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);
	$objets_selectionnes = lire_config('projets/objets', array());
	if (count($objets_selectionnes) > 0) {
		include_spip('base/objets');
		foreach ($objets_selectionnes as $key => $value) {
			$objets_selectionnes[$key] = objet_type($value);
		}
	}
	$objets_selectionnes = array_filter($objets_selectionnes);

	// auteurs sur les projets et cadres de projet
	if (!$e['edition'] AND in_array($e['type'], array('projet', 'projets_cadre'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if (!$e['edition'] AND in_array($e['type'], $objets_selectionnes)) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projets',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p = strpos($flux['data'], "<!--affiche_milieu-->")) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

/**
 * Ajout de liste sur la vue d'un auteur
 */
function projets_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/projets', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('projet:info_projets_auteur')
		), array('ajax' => true));

		$flux['data'] .= recuperer_fond('prive/objets/liste/projets_cadres', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('projets_cadre:info_projets_cadres_auteur')
		), array('ajax' => true));

	}

	return $flux;
}

/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param int $n
 *
 * @return int
 */
function projets_optimiser_base_disparus($flux) {
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('projet' => '*'), '*');

	return $flux;
}

/**
 * Insertion dans le pipeline revisions_chercher_label (Plugin révisions)
 * Trouver le bon label à afficher sur les champs dans les listes de révisions
 *
 * Si un champ est un champ extra, son label correspond au label défini du champs extra
 *
 * @pipeline revisions_chercher_label
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
 **/
function projets_revisions_chercher_label($flux) {
	if (isset($flux['args']['objet']) and $flux['args']['objet'] == 'projet') {
		foreach (array('id_projet', 'id_projet_parent', 'nom', 'url_site', 'id_projets_cadre', 'date_debut', 'date_livraison_prevue', 'date_livraison', 'nb_heures_estimees', 'nb_heures_reelles', 'actif', 'objectif', 'enjeux', 'methode', 'descriptif', 'date_publication', 'statut') as $champ) {
			if ($flux['args']['champ'] == $champ) {
				$flux['data'] = 'projet:label_' . $champ;
			}
		}
	}

	return $flux;
}
