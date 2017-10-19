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

	// pas de code saisi ?
	if (!$code_coupon) {
		return array('code_coupon' => _T('info_obligatoire_02'));
	}

	$coupon = sql_fetsel('*', 'spip_coupons', 'code = ' . sql_quote($code_coupon));

	// coupon inconnu ou déjà utilisé ?
	if (!$coupon['id_coupon'] || !coupon_utilisable($coupon['id_coupon'])) {
		return array('code_coupon' => _T('coupons:code_invalide'));
	}

	return array();
}

function formulaires_utiliser_coupon_traiter_dist() {
	$retours = array();

	$code_coupon = trim(_request('code_coupon'));
	$coupon      = sql_fetsel('*', 'spip_coupons', 'code = ' . sql_quote($code_coupon) . ' and id_commande = 0');

	if ($coupon['id_coupon']) {
		$id_commande = sql_getfetsel(
			'id_commande',
			'spip_commandes',
			'id_auteur = ' . $GLOBALS['visiteur_session']['id_auteur'] . ' AND statut = "encours"',
			'',
			'date DESC'
		);

		$montant_reduction = $coupon['montant'];

		// calculer le total des produits dans la commande, avec une éventuelle restriction sur la taxe
		$where = array('id_commande = ' . $id_commande);
		if (floatval($coupon['restriction_taxe'])) {
			$where[] = 'taxe = ' . $coupon['restriction_taxe'];
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

		// supprimer les occurences de ce coupon déjà présentes dans la commande
		sql_delete(
			'spip_commandes_details',
			'id_commande=' . $id_commande . ' and objet="coupon" and id_objet = ' . $coupon['id_coupon']);

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

