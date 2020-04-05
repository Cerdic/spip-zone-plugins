<?php

/**
 * Récupérer la liste des numéros de releases de Drupal
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

function recuperer_releases_drupal_dist() {
	// url page des releases = 'https://www.drupal.org/project/drupal/releases';
	// url de download de releases = 'https://ftp.drupal.org/files/projects/';
	$releases_drupal = file_get_contents('https://ftp.drupal.org/files/projects/');
	$releases = array();
	if (!empty($releases_drupal)) {
		preg_match_all("/href=\"drupal-([0-9].*)\.zip\"/", $releases_drupal, $releases);
	}
	$releases = $releases[1];
	$releases = array_filter($releases);
	$releases = array_unique($releases);
	natsort($releases);
	/**
	 * On ne va pas garder les versions dev, alpha, beta et rc pour ne garder que les versions stabilisées
	 */
	foreach ($releases as $index => $version) {
		if (preg_match('/(dev|alpha|beta|rc)/', $version)) {
			unset($releases[$index]);
		}
	}
	natsort($releases);
	spip_log(print_r($releases, true), 'info_sites');
	$releases = array_values($releases);

	return $releases;
}
