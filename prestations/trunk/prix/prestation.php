<?php
/**
 * Fonctions de calcul des prix d'une prestation
 *
 * @plugin     Prestations
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Prestations\Prix
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Permet d'obtenir le prix HT d'une prestation
 *
 * C'est le résultat de cette fonction qui est utilisée pour calculer le prix TTC.
 * Prix HT = quantité * prix unitaire HT
 *
 * @param int $id_prestation
 *     Identifiant de la prestation (paramètre inutilisé)
 * @param array $ligne
 *     Couples champs / valeurs décrivant la prestation
 *     Il faut au moins $ligne['quantite'] et $ligne['prix_unitaire_ht']
 * @return float
 *     Retourne le prix HT de la prestation sinon 0
 */
function prix_prestation_ht_dist($id_prestation, $ligne) {
	include_spip('prestations_fonctions');
	$prix = $ligne['prix_unitaire_ht'];
	$quantite = prestations_calculer_quantite($id_prestation, $ligne);
	
	// La quantité "0" ne voulant rien dire, cela signifie que ce n'est pas un produit quantifiable
	// mais des lignes en plus comme les frais de livraison, des frais de dossier, des déductions, etc
	if ($quantite > 0) {
		$prix = $quantite * $ligne['prix_unitaire_ht'];
	}

	return $prix;
}

/**
 * Permet d'obtenir le prix final TTC d'un détail d'une prestation
 *
 * Prix TTC = prix HT + (prix HT * taxe)
 *
 * @param int $id_prestation
 *     Identifiant de la prestation
 * @param float $prix_ht
 *     Prix HT de la prestation
 * @return float
 *     Retourne le prix TTC de la prestation sinon 0
 */
function prix_prestation_dist($id_prestation, $prix_ht) {
	$prix = $prix_ht;

	if (!function_exists('sql_fetsel')) {
		include_spip('base/abstract_sql');
	}
	$detail = sql_fetsel('*', 'spip_prestations', 'id_prestation = '.intval($id_prestation));

	if ($taxe = $detail['taxe']) {
		$prix += $prix*$taxe;
	}

	return $prix;
}
