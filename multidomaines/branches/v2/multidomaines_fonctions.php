<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function multidomaine_trouver_secteur($contexte){
	static $id_secteur_courant;
	if(!$id_secteur_courant) {
		if ($contexte['id_article']) {
			$id_secteur_courant = sql_getfetsel('id_secteur', 'spip_articles', 'id_article=' . $contexte['id_article']);
		} else if ($contexte['id_rubrique']) {
			$id_secteur_courant = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique=' . $contexte['id_rubrique']);
		}
	}
	return $id_secteur_courant;
}

function calculer_URL_SECTEUR($id_rubrique) {
	include_spip('inc/config');
	$id_secteur = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval($id_rubrique));
	$url = lire_config('multidomaines/editer_url_' .$id_secteur);
	if (empty($url)) {$url = lire_config('multidomaines/editer_url');}
	if (empty($url)) {$url = lire_config('adresse_site');}
	return trim($url,'/'). '/';
}

function calculer_URL_RUBRIQUE($id_rubrique) {
	include_spip('inc/config');
	$id_secteur = sql_getfetsel("id_rubrique", "spip_rubriques", "id_rubrique=" . intval($id_rubrique));
	$url = lire_config('multidomaines/editer_url_' .$id_secteur);
	if (empty($url)) {$url = lire_config('multidomaines/editer_url');}
	if (empty($url)) {$url = lire_config('adresse_site');}
	return trim($url,'/'). '/';
}

