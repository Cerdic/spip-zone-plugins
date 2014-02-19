<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function prix_produit_dist($id_objet, $prix_ht){
	$prix = $prix_ht;
	
	// S'il y a une taxe de définie explicitement dans le produit, on applique en priorité
	if (($id_produit = intval($id_objet)) > 0
		and include_spip('base/abstract_sql')
		and ($taxe = sql_getfetsel('taxe', 'spip_produits', 'id_produit = '.$id_produit)) !== null 
	){
		$prix += $prix*$taxe;
	}
	// Sinon on applique la taxe par défaut
	else{
		include_spip('inc/config');
		$prix += $prix*lire_config('produits/taxe', 0);
	}
	
	return $prix;
}

?>
