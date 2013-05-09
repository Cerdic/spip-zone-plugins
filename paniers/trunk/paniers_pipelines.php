<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Supprimer tous les paniers en cours qui sont trop vieux
function paniers_optimiser_base_disparus($flux){
	include_spip('inc/config');
	// On cherche la date depuis quand on a le droit d'avoir fait le panier
	$depuis_ephemere = date('Y-m-d H:i:s', time() - 3600*intval(lire_config('paniers/limite_ephemere', 24)));
	$depuis_enregistres = date('Y-m-d H:i:s', time() - 3600*intval(lire_config('paniers/limite_enregistres', 168)));
	
	// Soit le panier est à un anonyme donc on prend la limite éphémère, soit le panier appartient à un auteur et on prend l'autre limite
	$paniers = sql_allfetsel(
		'id_panier',
		'spip_paniers',
		'statut = '.sql_quote('encours').' and ((id_auteur=0 and date<'.sql_quote($depuis_ephemere).') or (id_auteur>0 and date<'.sql_quote($depuis_enregistres).'))'
	);
	if (is_array($paniers))
		$paniers = array_map('reset', $paniers);
	
	// S'il y a bien des paniers à supprimer
	if ($paniers){
		// Le in
		$in = sql_in('id_panier', $paniers);
		
		// On supprime d'abord les liens
		sql_delete(
			'spip_paniers_liens',
			$in
		);
		
		// Puis les paniers
		$nombre = intval(sql_delete(
			'spip_paniers',
			$in
		));
	}
	
	$flux['data'] += $nombre;
	return $flux;
}

// La CSS pour le panier
function paniers_insert_head_css($flux){
	$css = find_in_path('css/paniers.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}

?>
