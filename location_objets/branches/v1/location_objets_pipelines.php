<?php
/**
 * Utilisations de pipelines par Location d&#039;objets
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function location_objets_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/objets_locations', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('objets_location:info_objets_locations_auteur')
		), array('ajax' => true));
	}
	return $flux;
}

/**
 * Afficher le nombre d'éléments dans les parents
 *
 * @pipeline boite_infos
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function location_objets_boite_infos($flux) {
	if (isset($flux['args']['type']) and isset($flux['args']['id']) and $id = intval($flux['args']['id'])) {
		$texte = '';
		if ($flux['args']['type'] == 'objets_location' and $nb = sql_countsel('spip_objets_locations_details', array("statut='publie'", 'id_objets_location=' . $id))) {
			$texte .= '<div>' . singulier_ou_pluriel($nb, 'objets_locations_detail:info_1_objets_locations_detail', 'objets_locations_detail:info_nb_objets_locations_details') . "</div>\n";
		}
		if ($texte and $p = strpos($flux['data'], '<!--nb_elements-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		}
	}
	return $flux;
}

/**
 * Compter les enfants d'un objet
 *
 * @pipeline objets_compte_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function location_objets_objet_compte_enfants($flux) {
	if ($flux['args']['objet'] == 'objets_location' and $id_objets_location = intval($flux['args']['id_objet'])) {
		// juste les publiés ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['objets_locations_details'] = sql_countsel('spip_objets_locations_details', 'id_objets_location= ' . intval($id_objets_location) . " AND (statut = 'publie')");
		} else {
			$flux['data']['objets_locations_details'] = sql_countsel('spip_objets_locations_details', 'id_objets_location= ' . intval($id_objets_location) . " AND (statut <> 'poubelle')");
		}
	}

	return $flux;
}

/**
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 * Supprime les objets à la poubelle et encours.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function location_objets_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('objets_location'=>'*', 'objets_locations_detail'=>'*'), '*');

	sql_delete('spip_objets_locations', "statut IN ('poubelle','encours') AND maj < " . $flux['args']['date']);

	sql_delete('spip_objets_locations_details', "statut IN ('poubelle','encours') AND maj < " . $flux['args']['date']);

	return $flux;
}

/**
 * Définitions des notifications pour https://plugins.spip.net/notifications_archive.html
 *
 * @pipeline notifications_archive
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function location_objets_notifications_archive($flux) {
	$flux = array_merge($flux, array(
		'objets_location_client' => array(
			'activer' => 'on',
			'duree' => '180'
		),
		'objets_location_vendeur' => array(
			'duree' => '180'
		)
	));
	return $flux;
}

/**
 * Ajouter des contenus dans la partie <head> des pages de l’espace privé.
 *
 * @pipeline header_prive
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function location_objets_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="' . _DIR_PLUGIN_LOCATION_OBJETS  .'prive/css/location_objets.css" type="text/css" media="all" />';
	return $flux;
}

