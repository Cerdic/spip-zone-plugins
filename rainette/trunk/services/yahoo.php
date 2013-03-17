<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_YAHOO_URL_BASE'))
	define('_RAINETTE_YAHOO_URL_BASE', 'http://weather.yahooapis.com/forecastrss');
if (!defined('_RAINETTE_YAHOO_JOURS_PREVISION'))
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
	$index = 0;

	if (isset($flux['children']['channel'][0]['children']['item'][0]['children']['yweather:forecast'])) {
		$previsions = $flux['children']['channel'][0]['children']['item'][0]['children']['yweather:forecast'];

		foreach ($previsions as $attributes) {
			if (isset($attributes['attributes'])) {
				$prevision = $attributes['attributes'];

				// 1- Identifiants du jour : index dans le tableau et la date
				$tableau[$index]['index'] = $index;
				$date_jour = (isset($prevision['date'])) ? strtotime($prevision['date']) : '';
				$tableau[$index]['date'] = date('Y-m-d', $date_jour);

				// 2 Données astronomiques
				$tableau[$index]['lever_soleil'] = '';
				$tableau[$index]['coucher_soleil'] = '';

				// 3- Prévisions pour le jour
				$tableau[$index]['temperature_jour'] = (isset($prevision['high'])) ? intval($prevision['high']) : '';
				$tableau[$index]['code_icone_jour'] = (isset($prevision['code'])) ? intval($prevision['code']) : '';
				$tableau[$index]['vitesse_vent_jour'] = '';
				$tableau[$index]['angle_vent_jour'] = '';
				$tableau[$index]['direction_vent_jour'] = '';
				$tableau[$index]['risque_precipitation_jour'] = '';
				$tableau[$index]['humidite_jour'] = '';

				// 4- Prévisions pour la nuit
				$tableau[$index]['temperature_nuit'] = (isset($prevision['low'])) ? intval($prevision['low']) : '';
				$tableau[$index]['code_icone_nuit'] = '';
				$tableau[$index]['vitesse_vent_nuit'] = '';
				$tableau[$index]['angle_vent_nuit'] = '';
				$tableau[$index]['direction_vent_nuit'] = '';
				$tableau[$index]['risque_precipitation_nuit'] = '';
				$tableau[$index]['humidite_nuit'] = '';

				$index += 1;
			}
		}

		// Date de la mise à jour des prévisions
		// -- comme toutes les informations communes elles sont stockées dans un index supplémentaire en fin de tableau
		if ($tableau) {
			if (isset($flux['children']['channel'][0]['children']['lastbuilddate'][0]['text'])) {
				$date_maj = isset($flux['children']['channel'][0]['children']['lastbuilddate'][0]['text'])
						  ? strtotime($flux['children']['channel'][0]['children']['lastbuilddate'][0]['text'])
						  : '';
				$tableau[$index]['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
			}
		}
	}

	// Traitement des erreurs de flux
	$tableau[$index]['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}


function yahoo_flux2conditions($flux, $lieu) {

	static $tendance = array(0 => 'steady', 1 => 'rising', 2 => 'falling');
	$tableau = array();

	include_spip('inc/convertir');

	if (isset($flux['children']['channel'][0]['children']['yweather:wind'][0]['attributes'])) {
		$conditions = $flux['children']['channel'][0]['children']['yweather:wind'][0]['attributes'];

		// 1- Données anémométriques : vitesse, angle et direction (16 valeurs)
		// --- la direction est calculée car la valeur du service est en anglais
		$tableau['vitesse_vent'] = (isset($conditions['speed'])) ? floatval($conditions['speed']) : '';
		$tableau['angle_vent'] = (isset($conditions['direction'])) ? intval($conditions['direction']) : '';
		$tableau['direction_vent'] = (isset($conditions['direction'])) ? angle2direction($tableau['angle_vent']) : '';

		$tableau['temperature_ressentie'] = (isset($conditions['chill'])) ? intval($conditions['chill']) : '';
	}

	if (isset($flux['children']['channel'][0]['children']['item'][0]['children']['yweather:condition'][0]['attributes'])) {
		$conditions = $flux['children']['channel'][0]['children']['item'][0]['children']['yweather:condition'][0]['attributes'];

		// 2- Données d'observation : date et station
		// --- la station n'est pas précisée par le service
		$date_maj = (isset($conditions['date'])) ? strtotime($conditions['date']) : '';
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		$tableau['station'] = '';

		// 3- Températures : réelle et ressentie
		// --- La température ressentie est dans les données anémométriques
		$tableau['temperature_reelle'] = (isset($conditions['temp'])) ? intval($conditions['temp']) : '';

		// 4- Etat météorologique natif : code, icône et résumé
		//    Ces données sont stockées à titre conservatoire mais ne sont pas utilisées dans les modèles v2
		// --- le code météo est le même que celui de Weather ce qui permet d'utiliser les mêmes icônes
		// --- Il n'y a pas d'icône proposé par le service
		// --- la description n'est pas utilisée car toujours en anglais. On utilise le code méteo
		$tableau['code_meteo'] = (isset($conditions['code'])) ? intval($conditions['code']) : '';
		$tableau['icon_meteo'] = '';
		$tableau['desc_meteo'] = (isset($conditions['text'])) ? $conditions['text'] : '';

		// 5- Etat météorologique calculé : icône et résumé
		//    La traduction du resume dans la bonne langue est toujours faite par les fichiers de langue SPIP
		//    car l'API ne permet pas de choisir la langue. On ne stocke donc que le code meteo
		// --- icone et resume sont les données utilisées par les modèles v2
		$tableau['icone'] = $tableau['code_meteo'];
		$tableau['resume'] = $tableau['code_meteo'];
		// --- code_icone pour la compatibilité ascendante des modèles créés par les utilisateurs
		$tableau['code_icone'] = $tableau['code_meteo'];
		// --- La période jour/nuit n'est pas supportée par ce service car il n'y a pas de jeux d'icônes
		//     fonction de la période. Mais il est possible de la déterminer suivant l'heure de l'observation
		//     pour souci de cohérence avec les autres services mais elle ne sera pas utilisée pour l'instant
		//     TODO : determiner la periode jour ou nuit
		$tableau['periode'] = '';
	}

	if (isset($flux['children']['channel'][0]['children']['yweather:atmosphere'][0]['attributes'])) {
		$conditions = $flux['children']['channel'][0]['children']['yweather:atmosphere'][0]['attributes'];

		// 6- Données atmosphériques : humidité, point de rosée, pression et visibilité
		// --- pas de point de rosée fourni par le service
		// --- la tendance barométrique du service est convertie en texte comme pour les autres services
		$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity']) : '';
		$tableau['point_rosee'] = '';

		$tableau['pression'] = (isset($conditions['pressure'])) ? floatval($conditions['pressure']) : '';
		$tableau['tendance_pression'] = (isset($conditions['rising'])) ? $tendance[intval($conditions['rising'])] : '';

		$tableau['visibilite'] = (isset($conditions['visibility'])) ? floatval($conditions['visibility']) : '';
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}


function yahoo_flux2infos($flux, $lieu){
	$tableau = array();

	if (isset($flux['children']['channel'][0]['children']['yweather:location'][0]['attributes'])) {
		$infos = $flux['children']['channel'][0]['children']['yweather:location'][0]['attributes'];

		// 1- Appelation du lieu et de sa région
		$tableau['ville'] = $infos['city'];
		$tableau['ville'] .= (isset($infos['country'])) ? ', ' . $infos['country'] : '';
		$tableau['region'] = (isset($infos['region'])) ? $infos['region'] : '';

		if (isset($flux['children']['channel'][0]['children']['item'][0]['children'])) {
			$infos = $flux['children']['channel'][0]['children']['item'][0]['children'];

			// 2- Informations géographiques sur le lieu
			// --- la population et la zone DVD ne sont pas fournies par ce service
			$tableau['longitude'] = (isset($infos['geo:long'])) ? floatval($infos['geo:long'][0]['text']) : '';
			$tableau['latitude'] = (isset($infos['geo:lat'])) ? floatval($infos['geo:lat'][0]['text']) : '';

			$tableau['population'] = '';
		}
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

function yahoo_service2credits() {

	$credits = array('titre' => '', 'logo' => '');
	$credits['lien'] = 'http://weather.yahoo.com/';

	return $credits;
}

?>
