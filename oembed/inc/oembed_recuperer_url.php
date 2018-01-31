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
	$erreur = "";

	// on recupere le contenu de la page
	// si possible via curl en IPv4 car youtube bug en IPv6
	// uniquement si PHP >= 5.3.0 pour utiliser l'option CURLOPT_IPRESOLVE
	if (function_exists('curl_init') and version_compare(phpversion(), '5.3.0', '>=')){
		spip_log('Requete oembed (curl) pour ' . $url . ' : ' . $oembed_url, 'oembed.' . _LOG_DEBUG);
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $oembed_url);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		$browser = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:58.0) Gecko/20100101 Firefox/58.0';
		curl_setopt($c, CURLOPT_USERAGENT, $browser);
		//curl_setopt($c, CURLOPT_SSLVERSION, 1);

		if (isset($GLOBALS['meta']['http_proxy']) and $GLOBALS['meta']['http_proxy']){
			curl_setopt($c, CURLOPT_PROXY, $GLOBALS['meta']['http_proxy']);
			if (isset($GLOBALS['meta']['http_noproxy'])){
				curl_setopt($c, CURLOPT_NOPROXY, $GLOBALS['meta']['http_proxy']);
			}
		}

		// the real trick for Youtube :
		// http://stackoverflow.com/questions/26089067/youtube-oembed-api-302-then-503-errors
		curl_setopt($c, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$data = curl_exec($c);
		$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		if (!$data){
			$errno = curl_errno($c);
			$erreur = "Status $status Error " . curl_errno($c) . " " . curl_error($c);

			// si c'est une erreur de protocole SSL, on tente avec un exec mechant car ca peut venir de la version de CURL PHP
			// (ca marche au moins en local)
			if (!$data and $errno == 35) {
				exec('curl "'.$oembed_url.'"',$output);
				$data = implode("\n", $output);
			}

		}
		curl_close($c);

	}
	else {
		spip_log('Requete oembed (recuperer_page) pour '.$url.' : '.$oembed_url, 'oembed.'._LOG_DEBUG);
		include_spip('inc/distant');
		$data = recuperer_page($oembed_url);
	}

	if (!$data) {
		spip_log('infos oembed brutes pour '."$url | $oembed_url".' : ' . "ECHEC $erreur", 'oembed.'._LOG_ERREUR);
	}
	else {
		spip_log('infos oembed brutes pour '."$url | $oembed_url".' : '.(($format == 'html')?substr($data,0,100):$data), 'oembed.'._LOG_DEBUG);
	}

	if ($data) {
		if ($format == 'json') {
			$data = json_decode($data, true);
			$data['oembed_url_source'] = $url;
			$data['oembed_url'] = $oembed_url;
		}
		// TODO : format xml
		//if ($format == 'xml')
		//	$cache[$oembed_url] = false;
	}
	return $data;
}
