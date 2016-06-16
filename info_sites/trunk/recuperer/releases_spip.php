<?php

/**
 * Récupérer la liste des numéros de releases de SPIP
 *
 * @plugin     Info Sites
 * @copyright  2014-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Recuperer\ReleasesSpip
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function recuperer_releases_spip_dist() {
	$url_page_releases = 'https://core.spip.net/projects/spip/wiki';
	$content_page = file_get_contents($url_page_releases);
	preg_match_all("/>SPIP-v(.*)\.zip</", $content_page, $matches);
	$releases = array();

	foreach ($matches[1] as $version) {
		$version_reformatee = preg_replace('/-/', '.', $version);
		$releases[] = $version_reformatee;
	}
	natsort($releases);

	return $releases;
}
