<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Récupérer le contenu d'un flux xml, mais aussi HTML, XHTML, image, etc.
 * à partir d'une URL. Retourne un tableau contenant toutes les infos de l'entête
 * HTTP du server et le contenu de la page.
 *
 * @param string $url URL de la page à récupérer
 * @param string $login
 * @param string $password
 *
 * @return array|mixed
 */
function inc_recuperer_rss_commits_dist($url, $login = '', $password = '') {
	include_spip('inc/config');
	$header = array();

	// On teste si CURL est présent ou pas.
	// Sinon on passe par recuperer_page()
	if (function_exists('curl_init')) {
		// code adapté du script issu de cette page :
		// http://stackoverflow.com/a/14953910
		$options = array(
			// set request type post or get
			CURLOPT_CUSTOMREQUEST => "GET",
			// set to GET
			CURLOPT_POST => false,
			// return web page
			CURLOPT_RETURNTRANSFER => true,
			// don't return headers
			CURLOPT_HEADER => false,
			// follow redirects
			CURLOPT_FOLLOWLOCATION => true,
			// set referer on redirect
			CURLOPT_AUTOREFERER => true,
			// timeout on connect
			CURLOPT_CONNECTTIMEOUT => 120,
			// timeout on response
			CURLOPT_TIMEOUT => 120,
			// stop after 10 redirects
			CURLOPT_MAXREDIRS => 10,
			// stop after 10 redirects
			CURLOPT_SSL_VERIFYPEER => false,
		);

		if (isset($login) and $login != '') {
			$options[CURLOPT_USERPWD] = $login . ':' . $password;    // don't return headers
			/* spip_log($login . ' ' . $password, 'rss_commits'); */
		} else {
			$login = lire_config('rss_commits/login');
			$password = lire_config('rss_commits/password');
			$options[CURLOPT_USERPWD] = $login . ':' . $password;    // don't return headers
			// spip_log($login . ' ' . $password, 'rss_commits');
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		$err = curl_errno($ch);
		$errmsg = curl_error($ch);
		$header = curl_getinfo($ch);
		curl_close($ch);

		$header['errno'] = $err;
		$header['errmsg'] = $errmsg;
		$header['content'] = $content;

	} else {
		$header['content'] = recuperer_page($url);
	}

	return $header;
}

