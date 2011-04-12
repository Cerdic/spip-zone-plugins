<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Le prix HT d'un panier : addition des prix HT des objets liés
function prix_panier_ht_dist($id_panier, $ligne){
	$fonction_ht = charger_fonction('ht', 'inc/prix');
	$prix_ht = 0;
	
	// On va chercher tous les objets liés
	$objets = sql_allfetsel('objet, id_objet, quantite', 'spip_paniers_liens', 'id_panier = '.$id_panier);
	
	// Pour chaque objet on va chercher son prix HT x sa quantité
	if (is_array($objets)){
		foreach($objets as $objet){
			$prix_ht += $fonction_ht($objet['objet'], $objet['id_objet']) * $objet['quantite'];
		}
	}
	
	return $prix_ht;
}

// Le prix TTC d'un panier : addition des prix TTC des objets liés
function prix_panier_dist($id_panier, $prix_ht){
	$fonction_ttc = charger_fonction('prix', 'inc/');
	$prix = 0;
	
	// On va chercher tous les objets liés
	$objets = sql_allfetsel('objet, id_objet, quantite', 'spip_paniers_liens', 'id_panier = '.$id_panier);
	
	// Pour chaque objet on va chercher son prix TTC x sa quantité
	if (is_array($objets)){
		foreach($objets as $objet){
			$prix += $fonction_ttc($objet['objet'], $objet['id_objet']) * $objet['quantite'];
		}
	}
	
	return $prix;
}

?>
