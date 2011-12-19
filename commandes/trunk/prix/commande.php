<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Le prix HT d'une commande : total HT de ses détails
function prix_commande_ht_dist($id_commande, $ligne){
	$prix_ht = 0;
	
	// On va chercher tous les détails
	$details = sql_allfetsel('id_commandes_detail', 'spip_commandes_details', 'id_commande = '.$id_commande);
	
	if ($details and is_array($details)){
		$fonction_ht = charger_fonction('ht', 'inc/prix');
		$details = array_map('reset', $details);
		
		// Pour chaque objet on va chercher son prix HT x sa quantité
		foreach($details as $id_commandes_detail){
			$prix_ht += $fonction_ht('commandes_detail', $id_commandes_detail);
		}
	}
	
	return $prix_ht;
}

// Le prix TTC d'un élément d'une commande
function prix_commande_dist($id_commande, $prix_ht){
	$prix = 0;
	
	// On va chercher tous les détails
	$details = sql_allfetsel('id_commandes_detail', 'spip_commandes_details', 'id_commande = '.$id_commande);
	
	if ($details and is_array($details)){
		$fonction_ttc = charger_fonction('prix', 'inc/');
		$details = array_map('reset', $details);
		
		// Pour chaque objet on va chercher son prix TTC x sa quantité
		foreach($details as $id_commandes_detail){
			$prix += $fonction_ttc('commandes_detail', $id_commandes_detail);
		}
	}
	
	return $prix;
}

?>
