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
 * @param $id_commande
 *
 * @return bool
 */
function coupon_utilisable($id_coupon, $id_commande = null, $test_session = true) {
	if(!$id_commande && $test_session) {
		$id_commande = intval(session_get('id_commande'));
	}
	
	$utilisable = false;
	
	// le coupon est il actif et toujours valide ?
	if ($infos_coupon = sql_fetsel(
		'id_coupon, id_auteur',
		'spip_coupons',
		array(
			'id_coupon = ' . intval($id_coupon),
			'actif = ' . sql_quote('on'),
			'date_validite >= ' . sql_quote(date('Y-m-d H:i:s')),
		)
	)) {
		$utilisable = true;
		// le coupon est il restreint à un auteur en particulier ?
		if(!test_espace_prive() && $id_commande) {
			$id_auteur = sql_getfetsel('id_auteur','spip_commandes','id_commande='.$id_commande);
			if($id_auteur && $infos_coupon['id_auteur'] && $id_auteur != $infos_coupon['id_auteur']){
				$utilisable = false;
			}
		}
	}
	
	// reste t'il un montant utilisable sur le coupon ?
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
 * Calculer le montant de la réduction d'un coupon sur une commande ou sur un panier en fonction des taxes des objets
 *
 * @param $id_coupon integer
 * @param $id_commande integer or null
 * @param $id_panier integer
 * 
 * @return string
 */
function coupons_calculer_reduction_commande($id_coupon, $id_commande, $id_panier = null) {
	$id_coupon = intval($id_coupon);
	if (!intval($id_coupon) || (!intval($id_commande) && !intval($id_panier))) {
		return false;
	}

	$montant_reduction = coupon_montant_utilisable(intval($id_coupon));
	$infos_coupon  = sql_fetsel('id_produit, restriction_taxe', 'spip_coupons', 'id_coupon = ' . intval($id_coupon));
	$restriction_taxe = $infos_coupon['restriction_taxe'];
	$id_produit = $infos_coupon['id_produit'];
	
	// calculer le total des produits dans la commande / le panier
	// avec une éventuelle restriction sur la taxe
	if(intval($id_commande)) {
		$where = array('id_commande = ' . $id_commande);
		$table = 'spip_commandes_details';
		$champ_prix = 'prix_unitaire_ht';
	} else if(intval($id_panier)) {
		$where = array('id_panier = ' . $id_panier);
		$table = 'spip_paniers_liens pl join spip_produits p on(pl.objet="produit" and pl.id_objet=p.id_produit)';
		$champ_prix = 'prix_ht';
	}
	if (floatval($restriction_taxe)) {
		$where[] = 'taxe = ' . $restriction_taxe;
	}
	// ou sur un produit
	if($id_produit) {
		$where[] = 'objet="produit" and id_objet = ' . $id_produit;
	}
	$details = sql_allfetsel('*', $table, $where);

	$total_commande = 0;
	foreach ($details as $detail) {
		if (!in_array($detail['objet'], array('expedition', 'coupon'))) {
			$total_produit = $detail[$champ_prix] * $detail['quantite'] * (1 + $detail['taxe']);
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
 * Calculer le montant de la réduction d'un coupon sur un panier en fonction des taxes des objets
 *
 * @param $id_panier integer
 *
 * @return string
 */
function coupons_calculer_reduction_panier($id_coupon, $id_panier) {
	return coupons_calculer_reduction_commande($id_coupon, 0, $id_panier);
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

function coupon_ajouter_commande($id_coupon, $id_commande = null){
	include_spip('inc/session');
	
	if(!$id_commande) {
		$id_commande = intval(session_get('id_commande'));
	}
	
	if($id_commande) {
		// supprimer les occurences de coupons déjà présentes dans la commande
		$id_commande = intval(session_get('id_commande'));
		sql_delete(
			'spip_commandes_details',
			'id_commande=' . $id_commande . ' and objet="coupon"');
		
		$titre = sql_getfetsel('titre', 'spip_coupons', 'id_coupon=' . $id_coupon);

		$montant_reduction = coupons_calculer_reduction_commande($id_coupon, $id_commande);

		$id_commandes_detail = objet_inserer('commandes_detail');
		$valeurs             = array(
			'id_commande'      => $id_commande,
			'objet'            => 'coupon',
			'id_objet'         => $id_coupon,
			'descriptif'       => $titre,
			'quantite'         => 1,
			'prix_unitaire_ht' => 0 - $montant_reduction,
			'taxe'             => 0,
			'statut'           => 'attente',
		);
		objet_modifier('commandes_detail', $id_commandes_detail, $valeurs);

		// stocker le coupon en session
		session_set('id_coupon', $id_coupon);

		spip_log('ajout du coupon ' . $id_coupon . ' dans le détail de commande ' . $id_commandes_detail, 'coupons_commandes');
	}
	
}