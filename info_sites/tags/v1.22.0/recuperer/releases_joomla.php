<?php

/**
 * Récupérer la liste des numéros de releases de Joomla
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

function recuperer_releases_joomla_dist() {
	$releases = array();
	$releases_online = file_get_contents('https://downloads.joomla.org/cms/joomla10');
	if (!empty($releases_online)) {
		preg_match_all("/ ([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})/", $releases_online, $matches);
	}
	$releases = array_merge($releases, $matches[1]);
	$releases_online = file_get_contents('https://downloads.joomla.org/cms/joomla15');
	if (!empty($releases_online)) {
		preg_match_all("/ ([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})/", $releases_online, $matches);
	}
	$releases = array_merge($releases, $matches[1]);

	$releases_online = file_get_contents('https://downloads.joomla.org/cms/joomla25');
	if (!empty($releases_online)) {
		preg_match_all("/ ([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})/", $releases_online, $matches);
	}
	$releases = array_merge($releases, $matches[1]);

	$releases_online = file_get_contents('https://downloads.joomla.org/cms/joomla3');
	if (!empty($releases_online)) {
		preg_match_all("/ ([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})/", $releases_online, $matches);
	}
	$releases = array_merge($releases, $matches[1]);

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
