<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_RAINETTE_YAHOO_URL_BASE', 'http://weather.yahooapis.com/forecastrss');
define('_RAINETTE_YAHOO_JOURS_PREVISION', 2);

function yahoo_service2cache($lieu, $mode) {

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'yahoo');
	$f = $dir . strtoupper($lieu) . "_" . $mode . ".txt";

	return $f;
}


function yahoo_service2url($lieu, $mode) {

	include_spip('inc/config');
	$unite = lire_config('rainette/yahoo/unite', 'c');

	$url = _RAINETTE_YAHOO_URL_BASE . '?w=' . $lieu . '&u=' . $unite;

	return $url;
}


function yahoo_url2flux($url) {

	include_spip('inc/phraser');
	$xml = url2flux_xml($url, true);

	return $xml;
}


function yahoo_meteo2icone($meteo) {
	$icone = 'na';
	if (($meteo >= 0) && ($meteo < 48)) $icone = strval($meteo);

	return $icone;
}


/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * ne gere pas encore le jour et la nuit de la date courante suivant l'heure!!!!
 * @param array $xml
 * @return array
 */
function yahoo_flux2previsions($xml, $lieu) {
	$tableau = array();

	return $tableau;
}


function yahoo_flux2conditions($xml, $lieu) {
	$tableau = array();

	return $tableau;
}


function yahoo_flux2infos($flux, $lieu){
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($flux['children']['channel'][0]['children']['yweather:location'])) {
		$infos = $flux['children']['channel'][0]['children']['yweather:location'];

		$tableau['ville'] = $infos[0]['attributes']['city'];
		$tableau['ville'] .= (isset($infos[0]['attributes']['country'])) ? ', ' . $infos[0]['attributes']['country'] : '';
		$tableau['region'] = (isset($infos[0]['attributes']['region'])) ? $infos[0]['attributes']['region'] : '';
	}

	if (isset($flux['children']['channel'][0]['children']['item'][0]['children'])) {
		$infos = $flux['children']['channel'][0]['children']['item'][0]['children'];

		$tableau['longitude'] = (isset($infos['geo:long'])) ? round(floatval($infos['geo:long'][0]['text']), 2) : '';
		$tableau['latitude'] = (isset($infos['geo:lat'])) ? round(floatval($infos['geo:lat'][0]['text']), 2) : '';
	}

	// Informations non supportées par le service
	$tableau['population'] = '';
	$tableau['zone'] = '';

	return $tableau;
}

?>
