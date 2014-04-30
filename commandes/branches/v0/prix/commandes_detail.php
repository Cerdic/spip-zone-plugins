<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Le prix HT d'un élément d'une commande : la quantité x le prix unitaire
function prix_commandes_detail_ht_dist($id_commandes_detail, $ligne){
	// La quantité "0" ne voulant rien dire, cela signifie que ce n'est pas un produit quantifiable
	// mais des lignes en plus comme les frais de livraison, des frais de dossier, des déductions, etc
	if ($ligne['quantite'] > 0) return $ligne['quantite'] * $ligne['prix_unitaire_ht'];
	else return $ligne['prix_unitaire_ht'];
}

// Le prix TTC d'un élément d'une commande
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
