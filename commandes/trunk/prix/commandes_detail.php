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
 * Permet d'obtenir le prix HT d'un détail d'une commande.
 *
 * C'est le résultat de cette fonction qui est utilisée pour calculer le prix TTC.
 * Prix HT = quantité * prix unitaire HT
 *
 * @param int $id_commandes_detail
 *     Identifiant du détail (paramètre inutilisé)
 * @param array $ligne
 *     Couples champs / valeurs décrivant le détail
 *     Il faut au moins $ligne['quantite'] et $ligne['prix_unitaire_ht']
 * @return float
 *     Retourne le prix HT du détail sinon 0
 */
function prix_commandes_detail_ht_dist($id_commandes_detail, $ligne){
	// La quantité "0" ne voulant rien dire, cela signifie que ce n'est pas un produit quantifiable
	// mais des lignes en plus comme les frais de livraison, des frais de dossier, des déductions, etc
	$prix = $ligne['prix_unitaire_ht'];
	if ($ligne['quantite'] > 0) $prix = $ligne['quantite'] * $ligne['prix_unitaire_ht'];

	if (isset($ligne['reduction'])
	  and ($reduction = floatval($ligne['reduction']))>0) {
		$reduction = min($reduction, 1.0); // on peut pas faire une reduction de plus de 100%;
		$prix = $prix * (1.0 - $reduction);
	}

	return $prix;
}

/**
 * Permet d'obtenir le prix final TTC d'un détail d'une commande
 *
 * Prix TTC = prix HT + (prix HT * taxe)
 *
 * @param int $id_commandes_detail
 *     Identifiant du détail
 * @param float $prix_ht
 *     Prix HT du détail
 * @return float
 *     Retourne le prix TTC du détail sinon 0
 */
function prix_commandes_detail_dist($id_commandes_detail, $prix_ht){
	static $taxe_applicable = array();
	$prix = $prix_ht;

	if (!function_exists('sql_fetsel')) {
		include_spip('base/abstract_sql');
	}
	$detail = sql_fetsel('*', 'spip_commandes_details', 'id_commandes_detail = '.intval($id_commandes_detail));

	if (!isset($taxe_applicable[$detail['id_commande']])) {
		$taxe_applicable[$detail['id_commande']] = true;
		$taxe_exoneree_raison = sql_getfetsel('taxe_exoneree_raison', 'spip_commandes', 'id_commande='.intval($detail['id_commande']));
		if (strlen($taxe_exoneree_raison)) {
			$taxe_applicable[$detail['id_commande']] = false;
		}
	}

	if (
		$taxe_applicable[$detail['id_commande']]
		and ($taxe = $detail['taxe']) !== null
	){
		$prix += $prix*$taxe;
	}

	return $prix;
}

?>
