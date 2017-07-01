<?php
/**
 * Utilisations de pipelines par Liens associés
 *
 * @plugin     Liens associés
 * @copyright  2017
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Liens_associes\Pipelines
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
function liens_associes_affiche_milieu($flux) {
	include_spip('inc/config');
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// Liens associés sur les objets choisies
	if (!$e['edition'] and in_array($e['table_objet_sql'], array_filter(lire_config('liens_associes/objets', array ())))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array (
			'table_source' => 'associe_liens',
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
 * Appel de la pipeline jqueryui_plugins
 */

function liens_associes_jqueryui_plugins($scripts){
	if (_request('exec')) {
		$scripts[] = "jquery.ui.autocomplete";
	}

	return $scripts;
}


/**
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function liens_associes_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('associe_lien'=>'*'), '*');

	sql_delete('spip_associe_liens', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}
