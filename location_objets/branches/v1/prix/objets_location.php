<?php
/**
 * Fonctions de calcul des prix d'une commande et de ses détails
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Prix
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Permet d'obtenir le prix HT d'une location.
 *
 * Prix HT = total HT de ses détails
 *
 * @param int $id_commande
 *     Identifiant de la commande
 * @return float
 *     Retourne le prix HT de la commande sinon 0
 */
function prix_objets_location_ht_dist($id_objets_location) {
	$prix_ht = 0;

	// On va chercher tous les détails
	$details = sql_allfetsel(
		'id_objets_locations_detail',
		'spip_objets_locations_details',
		'id_objets_location = '.$id_objets_location);

	if ($details and is_array($details)) {
		$fonction_ht = charger_fonction('ht', 'inc/prix');
		$details = array_map('reset', $details);

		// Pour chaque détail on va chercher son prix HT x sa quantité
		foreach ($details as $id_objets_locations_detail) {
			$prix_ht += $fonction_ht('objets_locations_detail', $id_objets_locations_detail);
		}
	}

	return $prix_ht;
}

/**
 * Permet d'obtenir le prix final TTC d'une location
 *
 * Prix TTC = total TTC de ses détails
 *
 * @param int $id_commande
 *     Identifiant de la commande
 * @param float $prix_ht
 *     Prix HT de la commande, si null, on le calcule automatiquement
 * @return float
 *     Retourne le prix TTC de la commande sinon 0
 */
function prix_objets_location_dist($id_objets_location, $prix_ht = null) {
	if (is_null($prix_ht)) {
		$fonction_ht = charger_fonction('ht', 'prix/objets_location');
		$prix_ht = $fonction_ht($id_objets_location);
	}

	$prix = 0;

	// On va chercher tous les détails
	$details = sql_allfetsel(
		'id_objets_locations_detail',
		'spip_objets_locations_details',
		'id_objets_location = '.$id_objets_location);

	if ($details and is_array($details)) {
		$fonction_ttc = charger_fonction('prix', 'inc/');
		$details = array_map('reset', $details);

		// Pour chaque objet on va chercher son prix TTC x sa quantité
		foreach ($details as $id_objets_location_detail) {
			$prix += $fonction_ttc('objets_locations_detail', $id_objets_location_detail);
		}
	}

	return $prix;
}
