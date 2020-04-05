<?php
/**
 * Définit les pipelines du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function info_sites_affiche_milieu($flux) {
	include_spip('inc/utils');
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);
	$liste_objets = array(
		'organisations',
		'contacts',
		'projets',
		'projets_sites',
	);
	// projets_references sur les projets
	if (!$e['edition'] and in_array($e['type'], array('projet'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projets_references',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
		));
	}


	$liste_plugins = isset($GLOBALS['meta']['plugin']) ? unserialize($GLOBALS['meta']['plugin']) : array();

	// On regarde si le plugin rss_commits est actif.
	if (in_array('rss_commits', $liste_plugins)) {
		$liste_objets[] = 'commits';
	}
	if ($e == "accueil") {
		foreach ($liste_objets as $objet) {
			$texte .= recuperer_fond('prive/objets/liste/' . $objet);
		}
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
 * Insert header prive
 *
 * @param $flux
 *
 * @return string
 */
function info_sites_header_prive($flux) {
	include_spip('inc/utils');
	$css = find_in_path('lib/font-awesome/css/font-awesome.min.css');
	$flux .= '<link rel="stylesheet" href="' . $css . '" type="text/css" />';

	return $flux;
}

/**
 * Ajouter les tâches de CRON du plugin Info Sites
 *
 * @param  array $taches Tableau des tâches et leur périodicité en seconde
 *
 * @return array         Tableau des tâches et leur périodicité en seconde
 */
function info_sites_taches_generales_cron($taches) {
	$taches['maj_sites_plugins'] = 24 * 3600; // toutes les 24 heures
	$taches['recuperer_releases'] = 24 * 3600; // toutes les 24 heures

	return $taches;
}


function info_sites_compiler_branches_logiciel($flux) {
	if (is_array($flux['data']) and count($flux['data'])) {
		if (isset($flux['data']['drupal']) and count($flux['data']['drupal'])) {
			foreach ($flux['data']['drupal'] as $index => $branche) {
				$flux['data']['drupal'][$index] = strval(intval($branche));
			}
			$flux['data']['drupal'] = array_unique($flux['data']['drupal']);
		}
		if (isset($flux['data']['wordpress']) and count($flux['data']['wordpress'])) {
			foreach ($flux['data']['wordpress'] as $index => $branche) {
				$flux['data']['wordpress'][$index] = strval(intval($branche));
			}
			$flux['data']['wordpress'] = array_unique($flux['data']['wordpress']);
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
 *
 * @param  array $flux Données du pipeline
 *
 * @return array       Données du pipeline
 */
function info_sites_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('projets_reference' => '*'), '*');

	return $flux;
}
