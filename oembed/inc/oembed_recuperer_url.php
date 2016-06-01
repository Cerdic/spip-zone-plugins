<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Recuperer une URL oembed, si possible via curl et IPv4 pour contourner le bug de Youtube sur les IPv6
 *
 * @param string $oembed_url
 * @param string $url
 * @param string $format
 * @return bool|mixed|string
 */
function inc_oembed_recuperer_url($oembed_url, $url, $format) {
	$data = false;

	// on recupere le contenu de la page
	// si possible via curl en IPv4 car youtube bug en IPv6
	// uniquement si PHP >= 5.3.0 pour utiliser l'option CURLOPT_IPRESOLVE
	if (function_exists('curl_init') and version_compare(phpversion(), '5.3.0', '>=')) {
		spip_log('Requete oembed (curl) pour '.$url.' : '.$oembed_url, 'oembed.'._LOG_DEBUG);
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $oembed_url);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

		if (isset($GLOBALS['meta']['http_proxy']) and $GLOBALS['meta']['http_proxy']) {
		  curl_setopt($c, CURLOPT_PROXY, $GLOBALS['meta']['http_proxy']);
			if (isset($GLOBALS['meta']['http_noproxy'])) {
				curl_setopt($c, CURLOPT_NOPROXY, $GLOBALS['meta']['http_proxy']);
			}
		}

		// the real trick for Youtube :
		// http://stackoverflow.com/questions/26089067/youtube-oembed-api-302-then-503-errors
		curl_setopt($c, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$data = curl_exec($c);
		$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		curl_close($c);
	} else {
		spip_log('Requete oembed (recuperer_page) pour '.$url.' : '.$oembed_url, 'oembed.'._LOG_DEBUG);
		include_spip('inc/distant');
		$data = recuperer_page($oembed_url);
	}

	spip_log('infos oembed brutes pour '.$url.' : '.($data?$data:'ECHEC'), 'oembed.'._LOG_DEBUG);
	if ($data) {
		if ($format == 'json') {
			$data = json_decode($data, true);
		}
		// TODO : format xml
		//if ($format == 'xml')
		//	$cache[$oembed_url] = false;
	}

	return $data;
}
