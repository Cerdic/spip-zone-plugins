<?php
/**
 * Action : générer des pages uniques utiles
 *
 * Ne génère QUE les pages déclarées via le pipeline pages_uniques_utiles.
 *
 * @plugin     Pages
 * @copyright  2013-2019
 * @author     RastaPopoulos
 * @licence    GNU/GPL
 * @package    SPIP\Pages\Pipelines
 * @link       https://contrib.spip.net/Pages-uniques
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_generer_pages_uniques_utiles_dist($arg = null) {
	include_spip('pages_fonctions');
	include_spip('action/editer_objet');

	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$page = trim($arg);
	$pages_utiles = pages_uniques_utiles();

	// S'il y a un identifiant précis on ne garde que celui-là
	if ($page) {
		$pages_utiles = array_intersect_key($pages_utiles, array($page => 'oui'));
	}

	// On génère chaque page qui reste
	foreach ($pages_utiles as $page => $titre) {
		$set = array(
			'page'  => $page,
			'titre' => $titre,
		);
		objet_inserer('article', -1, $set);
	}
}
