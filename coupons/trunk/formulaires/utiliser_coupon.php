<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('action/editer_objet');

function formulaires_utiliser_coupon_charger_dist() {
	return array('code_coupon' => '');
}

function formulaires_utiliser_coupon_verifier_dist() {
	$code_coupon = trim(_request('code_coupon'));

	// supprimer les occurences de coupons déjà présentes dans la commande
	$id_commande = intval(session_get('id_commande'));
	sql_delete(
		'spip_commandes_details',
		'id_commande=' . $id_commande . ' and objet="coupon"');
	
	$id_coupon = sql_getfetsel(
		'id_coupon', 
		'spip_coupons', 
		'code = ' . sql_quote($code_coupon)
	);
	
	// coupon inconnu ou déjà utilisé ?
	if (!$id_coupon || !coupon_utilisable($id_coupon)) {
		return array('code_coupon' => _T('coupons:code_invalide'));
	}

	return array();
}

function formulaires_utiliser_coupon_traiter_dist() {
	$retours = array();

	$code_coupon = trim(_request('code_coupon'));
	$coupon      = sql_fetsel('*', 'spip_coupons', 'code = ' . sql_quote($code_coupon));

	if ($id_coupon = $coupon['id_coupon']) {
		
		$id_commande = intval(session_get('id_commande'));
		$montant_reduction = coupons_calculer_reduction_commande($id_coupon, $id_commande);

		$id_commandes_detail = objet_inserer('commandes_detail');
		$valeurs             = array(
			'id_commande'      => $id_commande,
			'objet'            => 'coupon',
			'id_objet'         => $coupon['id_coupon'],
			'descriptif'       => $coupon['titre'],
			'quantite'         => 1,
			'prix_unitaire_ht' => 0 - $montant_reduction,
			'taxe'             => 0,
			'statut'           => 'attente',
		);
		objet_modifier('commandes_detail', $id_commandes_detail, $valeurs);

		spip_log('ajout d\'un coupon dans le détail de commande ' . $id_commandes_detail, 'coupons_commandes');
		spip_log($valeurs, 'coupons_commandes');
	}

	return $retours;
}

