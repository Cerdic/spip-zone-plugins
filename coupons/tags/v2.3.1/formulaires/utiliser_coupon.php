<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('action/editer_objet');
include_spip('inc/session');

function formulaires_utiliser_coupon_charger_dist() {
	$code_coupon = '';
	if($id_coupon=session_get('id_coupon')){
		if(coupon_utilisable($id_coupon)){
			$code_coupon = sql_getfetsel('code','spip_coupons','id_coupon='.$id_coupon);
		}
	}
	return array('code_coupon' => $code_coupon);
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

	$montant_reduction = coupons_calculer_reduction_commande($id_coupon, $id_commande);
	
	// coupon sans réduction, inconnu ou déjà utilisé ?
	if (!$montant_reduction || !$id_coupon || !coupon_utilisable($id_coupon)) {
		return array('code_coupon' => _T('coupons:code_invalide'));
	}

	// stocker le coupon en session
	session_set('id_coupon',$id_coupon);

	return array();
}

function formulaires_utiliser_coupon_traiter_dist() {
	$retours = array();

	$code_coupon = trim(_request('code_coupon'));
	$coupon      = sql_fetsel('*', 'spip_coupons', 'code = ' . sql_quote($code_coupon));

	if ($id_coupon = $coupon['id_coupon']) {
		coupon_ajouter_commande($id_coupon);
	}

	return $retours;
}

