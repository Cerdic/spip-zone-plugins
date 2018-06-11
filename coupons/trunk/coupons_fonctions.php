<?php
/**
 * Fonctions utiles au plugin Coupons de réduction
 *
 * @plugin     Coupons de réduction
 * @copyright  2017
 * @author     Nicolas Dorigny
 * @licence    GNU/GPL
 * @package    SPIP\Coupons\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Une fonction qui indique si un coupon est utilisable ou pas (i.e. pas encore utilisé)
 *
 * @param $id_coupon
 *
 * @return bool
 */
function coupon_utilisable($id_coupon) {
	$utilisable = false;
	if ($id_coupon = sql_getfetsel(
		'id_coupon',
		'spip_coupons',
		array(
			'id_coupon = ' . intval($id_coupon),
			'actif = ' . sql_quote('on'),
			'date_validite >= ' . sql_quote(date('Y-m-d H:i:s')),
		)
	)) {
		$utilisable = true;
	}
	
	if ($id_coupon && !coupon_montant_utilisable($id_coupon)) {
		$utilisable = false;
	}

	return $utilisable;
}

/**
 * Une fonction qui retourne le montant qui reste à utiliser sur un coupon
 *
 * @param $id_coupon
 *
 * @return float
 */
function coupon_montant_utilisable($id_coupon) {
	$montant_coupon  = sql_getfetsel(
		'montant',
		'spip_coupons',
		'id_coupon = ' . intval($id_coupon)
	);
	$montant_utilise = sql_getfetsel(
		'sum(montant) as total',
		'spip_coupons_commandes',
		'id_coupon = ' . intval($id_coupon)
	);

	return $montant_coupon - $montant_utilise;
}

/**
 * Calculer le montant de la réduction d'un coupon sur une commande
 * en fonction des taxes des objets
 *
 * @return string
 */
function coupons_calculer_reduction_commande($id_coupon, $id_commande = null) {
	if (!$id_commande) {
		$id_commande = intval(session_get('id_commande'));
	}

	$montant_reduction = coupon_montant_utilisable($id_coupon);
	$infos_coupon  = sql_fetsel('id_produit, restriction_taxe', 'spip_coupons', 'id_coupon = ' . $id_coupon);
	$restriction_taxe = $infos_coupon['restriction_taxe'];
	$id_produit = $infos_coupon['id_produit'];
	
	// calculer le total des produits dans la commande
	// avec une éventuelle restriction sur la taxe
	$where = array('id_commande = ' . $id_commande);
	if (floatval($restriction_taxe)) {
		$where[] = 'taxe = ' . $restriction_taxe;
	}
	// ou sur un produit
	if($id_produit) {
		$where[] = 'objet="produit" and id_objet = ' . $id_produit;
	}
	$details = sql_allfetsel('*', 'spip_commandes_details', $where);

	$total_commande = 0;
	foreach ($details as $detail) {
		if (!in_array($detail['objet'], array('expedition', 'coupon'))) {
			$total_produit = $detail['prix_unitaire_ht'] * $detail['quantite'] * (1 + $detail['taxe']);
			if (floatval($detail['reduction']) > 0) {
				$reduction     = min(floatval($detail['reduction']), 1.0); // on peut pas faire une reduction de plus de 100%;
				$total_produit = $total_produit * (1.0 - $reduction);
			}
			$total_commande += $total_produit;
		}
	}

	// vérifier si le montant de la réduction est supérieur au total des produits
	if ($montant_reduction > $total_commande) {
		$montant_reduction = $total_commande;
	}

	return $montant_reduction;
}

/**
 * Génère un code coupon aléatoire et unique, avec un préfixe en option
 * On évite les I et O et 1 et 0 qui se ressemblent trop
 *
 * @param $prefixe  string
 * @param $longueur int
 *
 * @return string
 */
function coupon_generer_code($prefixe = '', $longueur = 10) {
	$chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";

	$code = ($prefixe ? $prefixe . '-' : '') . substr(str_shuffle($chars), 0, $longueur);
	while ($id_coupon = sql_getfetsel('id_coupon', 'spip_coupons', 'code = ' . sql_quote($code))) {
		$code = ($prefixe ? $prefixe . '-' : '') . substr(str_shuffle($chars), 0, $longueur);
	}

	return $code;
}