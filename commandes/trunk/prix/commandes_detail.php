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
	if ($ligne['quantite'] > 0) return $ligne['quantite'] * $ligne['prix_unitaire_ht'];
	else return $ligne['prix_unitaire_ht'];
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
	$prix = $prix_ht;

	if (
		include_spip('base/abstract_sql')
		and ($taxe = sql_getfetsel('taxe', 'spip_commandes_details', 'id_commandes_detail = '.$id_commandes_detail)) !== null
	){
		$prix += $prix*$taxe;
	}

	return $prix;
}

?>
