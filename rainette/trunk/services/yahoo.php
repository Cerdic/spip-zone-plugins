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
	$unite = lire_config('rainette/yahoo/unite', 'm');

	$url = _RAINETTE_YAHOO_URL_BASE . '?w=' . $lieu
		. '&u=' . ($unite == 'm' ? 'c' : 'f');

	return $url;
}


function yahoo_service2reload_time($mode) {

	static $reload = array('conditions' => 3600, 'previsions' => 7200);

	return $reload[$mode];
}


function yahoo_url2flux($url) {

	include_spip('inc/phraser');
	$flux = url2flux_xml($url, true);

	return $flux;
}


/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * ne gere pas encore le jour et la nuit de la date courante suivant l'heure!!!!
 * @param array $flux
 * @return array
 */
function yahoo_flux2previsions($flux, $lieu) {
	$tableau = array();

	return $tableau;
}


function yahoo_flux2conditions($flux, $lieu) {

	static $tendance = array(0 => 'steady', 1 => 'rising', 2 => 'steady');
	$tableau = array();

	include_spip('inc/convertir');

	if (isset($flux['children']['channel'][0]['children']['yweather:wind'][0]['attributes'])) {
		$conditions = $flux['children']['channel'][0]['children']['yweather:wind'][0]['attributes'];

		// Données aérologiques : vitesse, angle et direction (16 valeurs)
		// -- la direction est calculée car la valeur du service est en anglais
		$tableau['vitesse_vent'] = (isset($conditions['speed'])) ? intval($conditions['speed']) : '';
		$tableau['angle_vent'] = (isset($conditions['direction'])) ? intval($conditions['direction']) : '';
		$tableau['direction_vent'] = (isset($conditions['direction'])) ? angle2direction($tableau['angle_vent']) : '';
	}

	if (isset($flux['children']['channel'][0]['children']['item'][0]['children']['yweather:condition'][0]['attributes'])) {
		$conditions = $flux['children']['channel'][0]['children']['item'][0]['children']['yweather:condition'][0]['attributes'];

		// Données d'observation : date et station
		// -- la station n'est pas précisée par le service
		$date_maj = (isset($conditions['date'])) ? strtotime($conditions['date']) : '';
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		$tableau['station'] = '';

		// Températures : réelle et ressentie
		// -- La température ressentie est calculée
		$tableau['temperature_reelle'] = (isset($conditions['temp'])) ? intval($conditions['temp']) : '';
		$tableau['temperature_ressentie'] = round(temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']), 0);

		// Etat météorologique : code, icône et résumé natis au service
		// Ces données sont stockées à titre conservatoire mais ne sont pas utilisées dans les modèles v2
		// -- le code météo est le même que celui de Weather ce qui permet d'utiliser les mêmes icônes
		// -- Il n'y a pas d'icône proposé par le service
		// -- la description n'est pas utilisée car toujours en anglais. On utilise le code méteo
		$tableau['code_meteo'] = (isset($conditions['code'])) ? intval($conditions['code']) : '';
		$tableau['icon_meteo'] = '';
		$tableau['desc_meteo'] = (isset($conditions['text'])) ? $conditions['text'] : '';

		// La traduction du resume dans la bonne langue est toujours faite par les fichiers de langue SPIP
		// car l'API ne permet pas de choisir la langue. On ne stocke donc que le code meteo
		// Ce sont les données utilisées par les modèles v2
		$tableau['icone'] = $tableau['code_meteo'];
		$tableau['resume'] = $tableau['code_meteo'];
	}

	if (isset($flux['children']['channel'][0]['children']['yweather:atmosphere'][0]['attributes'])) {
		$conditions = $flux['children']['channel'][0]['children']['yweather:atmosphere'][0]['attributes'];

		// Données atmosphériques : humidité, point de rosée, pression et visibilité
		// -- pas de point de rosée fourni par le service
		// -- la tendance barométrique du service est convertie en texte comme pour les autres services
		$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity']) : '';
		$tableau['point_rosee'] = '';

		$tableau['pression'] = (isset($conditions['pressure'])) ? intval($conditions['pressure']) : '';
		$tableau['tendance_pression'] = (isset($conditions['rising'])) ? $tendance[intval($conditions['rising'])] : '';

		$tableau['visibilite'] = (isset($conditions['visibility'])) ? intval($conditions['visibility']) : '';
	}

	// TODO : determiner la periode jour ou nuit
	$tableau['periode'] = '';

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
