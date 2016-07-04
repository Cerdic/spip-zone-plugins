<?php

/**
 * Récupérer la liste des numéros de releases de Drupal
 *
 * @plugin     Info Sites
 * @copyright  2014-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Recuperer\ReleasesDrupal
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function recuperer_releases_drupal_dist() {
	$url_page_releases = 'https://www.drupal.org/project/drupal/releases';
	$content_page = file_get_contents($url_page_releases);
	preg_match("/<a title=\"Go to last page\" href=\"\/project\/drupal\/releases\?&amp;page=(\d+)\">last/", $content_page, $nb_page);
	$releases = array();
	$i = 0;
	while ($i <= $nb_page[1]) {
		$content_page = file_get_contents($url_page_releases . "?&page=" . $i);
		preg_match_all("/(<div class=\"field-label\">Official release from tag:&nbsp;<\/div><div class=\"field-items\"><div class=\"field-item even\">)(.*)(<\/div><\/div><\/div>)/", $content_page, $matches);
		$releases = array_merge($releases, $matches[2]);
	}
	natsort($releases);

	return $releases;
}
