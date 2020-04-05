<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Supprimer tous les paniers en cours qui sont trop vieux
function paniers_optimiser_base_disparus($flux) {
	include_spip('inc/config');
	// On cherche la date depuis quand on a le droit d'avoir fait le panier
	$depuis_ephemere    = date('Y-m-d H:i:s', time() - 3600 * intval(lire_config('paniers/limite_ephemere', 24)));
	$depuis_enregistres = date('Y-m-d H:i:s', time() - 3600 * intval(lire_config('paniers/limite_enregistres', 168)));

	// Soit le panier est à un anonyme donc on prend la limite éphémère, soit le panier appartient à un auteur et on prend l'autre limite
	$paniers = sql_allfetsel(
		'id_panier',
		'spip_paniers',
		'statut = ' . sql_quote('encours') . ' and ((id_auteur=0 and date<' . sql_quote($depuis_ephemere) . ') or (id_auteur>0 and date<' . sql_quote($depuis_enregistres) . '))'
	);
	if (is_array($paniers)) {
		$paniers = array_map('reset', $paniers);
	}

	// S'il y a bien des paniers à supprimer
	if ($paniers) {
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
function paniers_insert_head_css($flux) {
	$css  = timestamp(find_in_path('css/paniers.css'));
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

	return $flux;
}

/**
 * Sur une transformation de commande en attente
 * on supprime le panier source si besoin
 *
 * @param $flux
 *
 * @return @flux
 */
function paniers_post_edition($flux) {

	// Si on est dans le cas d'une commande qui passe de attente/en cours=>paye/livre/erreur
	if (isset($flux['args']['table']) and $flux['args']['table'] == 'spip_commandes'
		AND $id_commande = $flux['args']['id_objet']
		AND $flux['args']['action'] == 'instituer'
		AND isset($flux['data']['statut'])
		AND !in_array($flux['data']['statut'], array('attente', 'encours'))
		AND in_array($flux['args']['statut_ancien'], array('attente', 'encours'))
		AND $commande = sql_fetsel('id_commande, source', 'spip_commandes', 'id_commande=' . intval($id_commande))) {

		if (preg_match(",^panier#(\d+)$,", $commande['source'], $m)) {
			$id_panier        = intval($m[1]);
			$supprimer_panier = charger_fonction('supprimer_panier', 'action/');
			$supprimer_panier($id_panier);

			// nettoyer une eventuelle double commande du meme panier
			sql_updateq("spip_commandes", array('source' => ''), "source=" . sql_quote($commande['source']));
			#spip_log('suppression panier '.$id_panier,'paniers');
		}

	}

	return $flux;
}
