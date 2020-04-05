<?php

/**
 * Récupérer la liste des numéros de releases de Typo3
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Recuperer\ReleasesDrupal
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function recuperer_releases_typo3_dist() {
	$releases_online = file_get_contents('https://get.typo3.org/release-notes/');
	$releases = array();
	if (!empty($releases_online)) {
		preg_match_all("/<option value=\"([0-9].*)\">/", $releases_online, $releases);
	}
	$releases = $releases[1];
	$releases = array_filter($releases);
	$releases = array_unique($releases);
	natsort($releases);
	/**
	 * On ne va pas garder les versions dev, alpha, beta et rc pour ne garder que les versions stabilisées
	 */
	foreach ($releases as $index => $version) {
		if (preg_match('/(dev|alpha|beta|rc|snapshot)/', $version)) {
			unset($releases[$index]);
		}
	}
	$releases = array_values($releases);
	natsort($releases);
	spip_log(print_r($releases, true), 'info_sites');

	return $releases;
}
