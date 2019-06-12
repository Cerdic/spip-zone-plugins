<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2019                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Fix https://core.spip.net/issues/4345
 *
 * Action pour exécuter le cron de manière asynchrone si le serveur le permet
 *
 * @package SPIP\Core\Genie
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Url pour lancer le cron de manière asynchrone si le serveur le permet
 *
 * Cette fonction se termine tout de suite, après avoir lancée un cron asynchrone
 * Elle est inadaptée depuis un cron UNIX : il faut directement appeler l'action cron
 *
 * @see queue_affichage_cron() Dont une partie du code est repris ici.
 * @see action_cron() URL appelée en asynchrone pour excécuter le cron
 */
function action_super_cron_dist() {
	// Si fsockopen est possible, on lance le cron via un socket
	// en asynchrone
	if (function_exists('fsockopen')) {
		$url = generer_url_action('cron');

		// si nécessaire, pour être certain de contourner les caches de temps en temps
		if (defined('_SUPER_CRON_DELAIS')) {
			$t = intval(time()/_SUPER_CRON_DELAIS);
			$url = parametre_url($url, 't', $t);
		}

		$parts = parse_url($url);
		$host_protocol = '';
		if (substr($url, 0, 5) == 'https') {
			if (empty($parts['port'])) {
				$parts['port'] = 443;
			}
			$host_protocol = 'ssl://';
		}
		elseif (empty($parts['port'])) {
			$parts['port'] = 80;
		}

		$fp = fsockopen(
			$host_protocol.$parts['host'],
			$parts['port'],
			$errno,
			$errstr,
			30
		);
		if ($fp) {
			$out = "GET " . $parts['path'] . "?" . $parts['query'] . " HTTP/1.1\r\n";
			$out .= "Host: " . $parts['host'] . "\r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
			fclose($fp);
			return;
		}
		else
			spip_log ("super_cron : pas pu fsockopen $url : errno=$errno errstr=$errstr".$parts['host'], "cron");
	}
	else
		spip_log ("super_cron : manque fsockopen", "cron");
	// ici lancer le cron par un CURL asynchrone si CURL est présent
	// TBD

	return;
}
