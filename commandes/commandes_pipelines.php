<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// La CSS pour une commande
function commandes_insert_head_css($flux){
	$css = find_in_path('css/commandes.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}

// Supprimer toutes les commandes en cours qui sont trop vieilles
function commandes_optimiser_base_disparus($flux){
	include_spip('inc/config');
	// On cherche la date depuis quand on a le droit d'avoir fait la commande (par défaut 1h)
	$depuis = date('Y-m-d H:i:s', time() - 3600);
	
	// On récupère les commandes trop vieilles
	$commandes = sql_allfetsel(
		'id_commande',
		'spip_commandes',
		'statut = '.sql_quote('encours').' and date<'.sql_quote($depuis).')'
	);
	if (is_array($commande))
		$commandes = array_map('reset', $commandes);
	
	// S'il y a bien des commandes à supprimer
	if ($commandes){
		// Le in
		$in = sql_in('id_commande', $commandes);
		
		// On supprime d'abord les détails
		sql_delete(
			'spip_commandes_details',
			$in
		);
		
		//On cherche des adresses attachées
		if ($adresses_commande = sql_allfetsel(
			'id_adresse', 
			'spip_adresses_liens', 
			array(
				'objet = '.sql_quote('commande'), 
				$in
			)
		)){
			$adresses_commande = array_map('reset', $adresses_commande);
			$in2 = sql_in('id_adresse', $adresses_commande);
			sql_delete('spip_adresses_liens', $in2);
			sql_delete('spip_adresses', $in2);
		}
		
		// Puis les commandes
		$nombre = intval(sql_delete(
			'spip_commandes',
			$in
		));
	}
	
	$flux['data'] += $nombre;
	return $flux;
}

?>
