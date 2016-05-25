<?php
/**
 * Définit les pipelines du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function info_sites_affiche_milieu($flux) {
	$liste_objets = array(
		'organisations',
		'contacts',
		'projets',
		'projets_sites',
	);

	$liste_plugins = isset($GLOBALS['meta']['plugin']) ? unserialize($GLOBALS['meta']['plugin']) : array();

	// On regarde si le plugin rss_commits est actif.
	if (in_array('rss_commits', $liste_plugins)) {
		$liste_objets[] = 'commits';
	}
	if ($flux["args"]["exec"] == "accueil") {
		foreach ($liste_objets as $objet) {
			$flux["data"] .= recuperer_fond('prive/objets/liste/' . $objet);
		}
	}

	return $flux;
}

/**
 * Insert header prive
 * @param $flux
 *
 * @return string
 */
function info_sites_header_prive($flux) {
	include_spip('inc/utils');
	$css = find_in_path('lib/font-awesome/css/font-awesome.min.css');
	$flux .= '<link rel="stylesheet" href="'
	. $css
	. '" type="text/css" />';

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
