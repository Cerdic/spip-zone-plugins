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

function balise_NOM_SITE_MULTIDOMAINE_dist($p) {
	$id_rubrique = interprete_argument_balise(1, $p);
	if (strlen(trim($id_rubrique)) == 0) {
		$id_rubrique = calculer_balise('id_rubrique', $p)->code;
	}
	$p->code              = "calculer_nom_site_multidomaine(intval($id_rubrique))";
	$p->interdire_scripts = false;

	return $p;
}

function calculer_nom_site_multidomaine($id_rubrique){
	$nom_site = $GLOBALS['meta']['nom_site'];
	$cfg = lire_config('multidomaines');
	foreach ($cfg as $id_rubrique_domaine => $config) {
		if(is_int($id_rubrique_domaine) && $config['url']){
			$branche = explode(',',calcul_branche_in($id_rubrique_domaine));
			if(is_array($branche) && in_array($id_rubrique, $branche) ){
				$nom_site = sql_getfetsel('titre','spip_rubriques','id_rubrique = '.$id_rubrique_domaine);
			}
		}
	}
	$nom_site = supprimer_numero(typo($nom_site));
	
	return $nom_site;
}