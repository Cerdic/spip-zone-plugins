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

	// remonter les rubriques jusqu'à trouver une url multidomaine
	include_spip('inc/config');
	$url = lire_config("multidomaines/$id_rubrique/url");
	$id_rubrique_courante = $id_rubrique;
	while (!$url && $id_rubrique_courante) {
		$id_parent = sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=" . intval($id_rubrique_courante));
		$url = lire_config("multidomaines/$id_parent/url");
		$id_rubrique_courante = $id_parent;
	}

	// sinon, url par défaut
	if (empty($url)) {
		$url = lire_config('multidomaines/defaut/url');
	}
	if (empty($url)) {
		$url = lire_config('adresse_site');
	}

	// mettre à jour le cache
	$urls_cache[$id_rubrique] = trim($url, '/') . '/';

	return $urls_cache[$id_rubrique];
}
