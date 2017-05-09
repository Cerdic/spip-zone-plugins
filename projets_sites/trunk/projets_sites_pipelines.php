<?php
/**
 * Utilisations de pipelines par `Sites pour projets`
 *
 * @plugin     Sites pour projets
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter les tâches de CRON du plugin `Sites pour projets`
 *
 * @param  array $taches Tableau des tâches et leur périodicité en seconde
 *
 * @return array         Tableau des tâches et leur périodicité en seconde
 */
function projets_sites_taches_generales_cron($taches) {
	$taches['maj_webservice'] = 7 * 24 * 3600; // tous les 7 jours
	$taches['projetssites_nettoyage'] = 7 * 24 * 3600; // tous les 7 jours
	return $taches;
}


/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 *
 * @param  array $flux Données du pipeline
 *
 * @return array       Données du pipeline
 */
function projets_sites_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);


	// projets_sites sur les projets
	if (!$e['edition'] and in_array($e['type'], array('projet'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projets_sites',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
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
 * Optimiser la base de données en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 *
 * @param  array $flux Données du pipeline
 *
 * @return array       Données du pipeline
 */
function projets_sites_optimiser_base_disparus($flux) {
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('projets_site' => '*'), '*');

	return $flux;
}

/**
 * Insert header prive
 *
 */
function projets_sites_header_prive($flux) {
	$flux .= '<script type="application/javascript" src="'
		. _DIR_PLUGIN_PROJETS_SITES
		. 'javascript/projets_sites_prive.js"></script>';

	return $flux;
}

/**
 * Ce pipeline permet de rajouter des écritures de noms de logiciels d'un site
 *
 * @param $flux
 */
function projets_sites_lister_logiciels_noms($flux) {

	$raccourcis = array(
		'WP' => 'WordPress',
		'WordPress' => 'WordPress',
		'Wordpress' => 'WordPress',
		'wordpress' => 'WordPress',
		'worpdress' => 'WordPress', // Easter egg :-)
		'Worpdress' => 'WordPress', // Easter egg :-)
		'Drupal' => 'Drupal',
		'drupal' => 'Drupal',
		'Spip' => 'SPIP',
		'SPIP' => 'SPIP',
		'spip' => 'SPIP',
		'slip' => 'SPIP', // Easter egg :-)
		'Slip' => 'SPIP', // Easter egg :-)
		'Typo 3' => 'TYPO3',
		'Typo3' => 'TYPO3',
		'typo3' => 'TYPO3',
		'typo 3' => 'TYPO3',
	);
	$flux['data'] = array_merge($flux['data'], $raccourcis);

	return $flux;

}
