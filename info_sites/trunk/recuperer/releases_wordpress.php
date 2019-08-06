<?php

/**
 * Récupérer la liste des numéros de releases de Wordpress
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Recuperer\ReleasesWordpress
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function recuperer_releases_wordpress_dist() {
	$url_page_releases = 'https://codex.wordpress.org/WordPress_Versions';
	$releases = array();
	$content_page = file_get_contents($url_page_releases);
	preg_match_all("/href=\"https:\/\/wordpress\.org\/support\/wordpress-version\/.*\">([0-9].*)<\/a>/", $content_page, $matches);

	if (is_array($matches) and count($matches) > 0) {
		$releases = array_merge($releases, $matches[1]);
	}
	natsort($releases);

	return $releases;
}
