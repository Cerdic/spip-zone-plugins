<?php
/**
 * Configuration
 *
 * @plugin     URLs Personnalisées étendues
 * @copyright  2013
 * @author     Charles Razack
 * @licence    GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_urls_pages_charger_dist(){
	include_spip('inc/config');
	include_spip('inc/urls_pages');

	$config = lire_config('urls_pages');
	$valeurs['rewritebase'] = $config['rewritebase'];
	$pages = urls_pages_lister_pages();
	$valeurs['pages'] = $pages;
	if ( is_array($pages) and count($pages) ) {
		// pages répertoriées
		foreach ( $pages as $page => $dossier )
			$valeurs[$page] = $config[$page];
		// pages enregistrées en config mais non repertoriées
		if ( is_array($config) and count($config) )
			foreach ( array_filter($config) as $page_config => $rewrite ) {
				if ( !in_array($page_config, array_keys($pages))
				  and $page_config != 'rewritebase' ) {
					$obsoletes[] = $page_config;
					$valeurs[$page_config] = $rewrite;
				}
			}
	}
	$valeurs['obsoletes'] = $obsoletes;

	return $valeurs;
}

function formulaires_configurer_urls_pages_verifier_dist(){
	include_spip('inc/urls_pages');

	// recupérer les valeurs postées
	$pages = urls_pages_lister_pages();
	if ( is_array($pages) and count($pages) ) {
		foreach ( $pages as $page => $dossier )
			$valeurs[$page] = _request($page);
	}
	$valeurs = array_filter($valeurs);

	// vérifier que les urls sont libres
	if ( is_array($valeurs) and count($valeurs) ) {
		foreach ( $valeurs as $page => $valeur ) {
			if ( sql_countsel('spip_urls', array("url=" .sql_quote($valeur))) )
				$erreurs[$page] = _T('urls_pages:erreur_url_non_libre');
		}
	}

	// vérifier qu'il n'y a pas plusieurs fois la même url saisie
	$doublons = array_count_values($valeurs);
	if ( is_array($doublons) and count($doublons) )
		foreach ($doublons as $valeur=>$nb)
			if ( $nb > 1 )
				foreach( array_keys($valeurs, $valeur) as $page)
					$erreurs[$page] = _T('urls_pages:erreur_url_doublon');

	return $erreurs;
}
