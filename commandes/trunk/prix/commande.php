<?php
/**
 * Fonctions de calcul des prix d'une commande et de ses détails
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Prix
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Permet d'obtenir le prix HT d'une commande.
 *
 * Prix HT = total HT de ses détails
 *
 * @param int $id_commande
 *     Identifiant de la commande
 * @return float
 *     Retourne le prix HT de la commande sinon 0
 */
function prix_commande_ht_dist($id_commande) {
	$prix_ht = 0;

	// On va chercher tous les détails
	$details = sql_allfetsel('id_commandes_detail', 'spip_commandes_details', 'id_commande = '.$id_commande);

	if ($details and is_array($details)) {
		$fonction_ht = charger_fonction('ht', 'inc/prix');
		$details = array_map('reset', $details);

		// Pour chaque détail on va chercher son prix HT x sa quantité
		foreach ($details as $id_commandes_detail) {
			$prix_ht += $fonction_ht('commandes_detail', $id_commandes_detail);
		}
	}

	return $prix_ht;
}

/**
 * Permet d'obtenir le prix final TTC d'une commande
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
function prix_commande_dist($id_commande, $prix_ht = null) {
	if (is_null($prix_ht)) {
		$fonction_ht = charger_fonction('ht', 'prix/commande');
		$prix_ht = $fonction_ht($id_commande);
	}

	$prix = 0;

	// On va chercher tous les détails
	$details = sql_allfetsel('id_commandes_detail', 'spip_commandes_details', 'id_commande = '.$id_commande);

	if ($details and is_array($details)) {
		$fonction_ttc = charger_fonction('prix', 'inc/');
		$details = array_map('reset', $details);

		// Pour chaque objet on va chercher son prix TTC x sa quantité
		foreach ($details as $id_commandes_detail) {
			$prix += $fonction_ttc('commandes_detail', $id_commandes_detail);
		}
	}

	return $prix;
}
