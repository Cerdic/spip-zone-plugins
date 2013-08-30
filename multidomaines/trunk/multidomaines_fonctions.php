<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

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

?>
