<?php
/**
 * Fonctions de calcul des prix d'une location et de ses détails
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Prix
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Permet d'obtenir le prix HT d'un détail d'une location.
 *
 * C'est le résultat de cette fonction qui est utilisée pour calculer le prix TTC.
 * Prix HT = quantité * prix unitaire HT
 *
 * @param int $id_commandes_detail
 *        	Identifiant du détail (paramètre inutilisé)
 * @param array $ligne
 *        	Couples champs / valeurs décrivant le détail
 *        	Il faut au moins $ligne['quantite'] et $ligne['prix_unitaire_ht']
 * @return float Retourne le prix HT du détail sinon 0
 */
function prix_objets_locations_detail_ht_dist($id_objets_locations_detail, $ligne) {
	$prix = $ligne['prix_unitaire_ht'];
	if ($ligne['quantite'] > 0) {
		$prix = $ligne['quantite'] * $ligne['prix_unitaire_ht'];
	}

	if ($ligne['prix_total'] == FALSE) {
		$prix = $prix * $ligne['duree'];
	}

	if (isset($ligne['reduction']) and ($reduction = floatval($ligne['reduction'])) > 0) {
		$reduction = min($reduction, 1.0); // on peut pas faire une reduction de plus de 100%;
		$prix = $prix * (1.0 - $reduction);
	}

	return $prix;
}

/**
 * Permet d'obtenir le prix final TTC d'un détail d'une location
 *
 * Prix TTC = prix HT + (prix HT * taxe)
 *
 * @param int $id_commandes_detail
 *        	Identifiant du détail
 * @param float $prix_ht
 *        	Prix HT du détail
 * @return float Retourne le prix TTC du détail sinon 0
 */
function prix_objets_locations_detail_dist($id_objets_locations_detail, $prix_ht) {
	$prix = $prix_ht;

	if (!function_exists('sql_fetsel')) {
		include_spip('base/abstract_sql');
	}
	$detail = sql_fetsel(
		'*',
		'spip_objets_locations_details',
		'id_objets_locations_detail = ' . intval($id_objets_locations_detail));

	if (($taxe = $detail['taxe']) !== null) {
		$prix = $prix + $taxe;
	}

	return $prix;
}
