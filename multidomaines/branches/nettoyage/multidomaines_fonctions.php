<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function multidomaine_trouver_secteur($contexte) {
	static $id_secteur_courant;
	if (!$id_secteur_courant) {
		if ($contexte['id_article']) {
			$id_secteur_courant = sql_getfetsel('id_secteur', 'spip_articles', 'id_article=' . $contexte['id_article']);
		} else if ($contexte['id_rubrique']) {
			$id_secteur_courant = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique=' . $contexte['id_rubrique']);
		}
	}

	return $id_secteur_courant;
}

function calculer_URL_SECTEUR($id_rubrique) {
	// mettre en cache les calculs
	static $urls_cache = array();
	if (isset($urls_cache[$id_rubrique])) {
		return $urls_cache[$id_rubrique];
	}

	// Remonter les rubriques jusqu'à trouver une URL multidomaine
	include_spip('inc/config');
	$cfg = lire_config('multidomaines');
	$id_rubrique_courante = $id_rubrique;
	// Soit on est déjà dans un secteur
	if (isset($cfg[$id_rubrique]['url'])) {
		$url = $cfg[$id_rubrique]['url'];
	// Soit on remonte jusqu'au secteur
	} else {
		while (!$url && $id_rubrique_courante) {
			$id_parent = sql_getfetsel('id_parent', 'spip_rubriques', 'id_rubrique=' . intval($id_rubrique_courante));
			$url = isset($cfg[$id_parent]['url']) ? $cfg[$id_parent]['url'] : false;
			$id_rubrique_courante = $id_parent;
		}
	}

	// sinon, URL par défaut
	if (empty($url)) {
		$url = lire_config('multidomaines/defaut/url');
	}
	// Sinon, URL du site
	if (empty($url)) {
		$url = lire_config('adresse_site');
	}

	// mettre à jour le cache
	$urls_cache[$id_rubrique] = trim($url, '/') . '/';

	return $urls_cache[$id_rubrique];
}
