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
	if ( is_array($pages) ) {
		// pages répertoriées
		foreach ( $pages as $page => $dossier )
			$valeurs[$page] = $config[$page];
		// pages enregistrées en config mais non repertoriées
		if ( is_array($config) )
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
