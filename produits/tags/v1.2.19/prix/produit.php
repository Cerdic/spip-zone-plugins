<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function prix_produit_dist($id_objet, $prix_ht) {
	$prix = $prix_ht;

	// S'il y a une taxe de définie explicitement dans le produit, on applique en priorité
	if (($id_produit = intval($id_objet)) > 0
		and include_spip('base/abstract_sql')
		and ($taxe = floatval(sql_getfetsel('taxe', 'spip_produits', 'id_produit = '.$id_produit))) !== 0
	) {
		$prix += $prix*$taxe;
	}

	return $prix;
}