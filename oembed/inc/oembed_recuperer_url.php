<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function inc_oembed_recuperer_url($data_url,$url,$format){
	$data = false;

	// on recupere le contenu de la page
	// si possible via curl en IPv4 car youtube bug en IPv6
	if (function_exists('curl_init')){
		spip_log('Requete oembed (curl) pour '.$url.' : '.$data_url,'oembed.'._LOG_DEBUG);
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $data_url);
		// the real trick for Youtube :
		// http://stackoverflow.com/questions/26089067/youtube-oembed-api-302-then-503-errors
		curl_setopt($c, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$data = curl_exec($c);
		$status = curl_getinfo($c,CURLINFO_HTTP_CODE);
		curl_close($c);
	}
	else {
		spip_log('Requete oembed (recuperer_page) pour '.$url.' : '.$data_url,'oembed.'._LOG_DEBUG);
		include_spip('inc/distant');
		$data = recuperer_page($data_url);
	}

	spip_log('infos oembed brutes pour '.$url.' : '.($data?$data:'ECHEC'),'oembed.'._LOG_DEBUG);
	if ($data) {
		if ($format == 'json')
			$data = json_decode($data,true);
		// TODO : format xml
		//if ($format == 'xml')
		//	$cache[$data_url] = false;
	}

	return $data;
}