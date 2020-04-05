<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Calculer le prix HT des produits
 *
 * @param $id_objet
 * @param $ligne
 *
 * @return mixed
 */
function prix_option_produit_ht($objet, $id_objet, $options, $ligne) {
	$options = explode('|',trim($options,'|'));
	$prix_option_ht = 0;
	foreach ($options as $option) {
		$prix_option_ht += sql_getfetsel(
			'prix_option_objet',
			'spip_options_liens',
			array(
				'id_option = ' . intval($option),
				'objet = ' . sql_quote($objet),
				'id_objet = ' . intval($id_objet),
			)
		);	
	}

	return $ligne['prix_ht'] + $prix_option_ht;
}

/**
 * Calculer le prix TTC des produits
 *
 * @param $id_objet
 * @param $prix_ht
 *
 * @return mixed
 */
function prix_option_produit($objet, $id_objet, $options, $prix_ht) {
	include_spip('base/abstract_sql');

	if ($taxe = sql_getfetsel('taxe', 'spip_produits', 'id_produit = ' . $id_objet)) {
		$prix_ttc = $prix_ht * (1 + $taxe);
	}

	return $prix_ttc;
}