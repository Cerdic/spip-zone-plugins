<?php
/**
 * Utilisations de pipelines par Disponibilites objets
 *
 * @plugin     Disponibilites objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Objets_disponibilites\Pipelines
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
function objets_disponibilites_affiche_milieu($flux) {
	include_spip('inc/config');
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);
	$objets_cibles = lire_config('objets_disponibilites/objets', array());



	// Objets_informations sur les objets choisis.
	if (!$e['edition'] and in_array($e['table_objet_sql'], $objets_cibles)) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'disponibilite_dates',
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
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function objets_disponibilites_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('disponibilite_date'=>'*'), '*');

	return $flux;
}
