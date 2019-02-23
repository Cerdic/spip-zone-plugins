<?php
/**
 * Utilisations de pipelines par Services extras pour objets
 *
 * @plugin     Services extras pour objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Objets_services_extras\Pipelines
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
function objets_services_extras_affiche_milieu($flux) {
	include_spip('inc/config');
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);
	$objets_cibles = lire_config('objets_infos_extras/objets', array());

	// objets_services sur les objets choisis
	if (!$e['edition'] and in_array($e['table_objet_sql'], $objets_cibles)) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'objets_services',
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
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function objets_services_extras_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('objets_service'=>'*'), '*');

	sql_delete('spip_objets_services', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}
