<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service Open Weather Map (owm).
 * Ce service fournit des données au format XML ou JSON.
 *
 * @package SPIP\RAINETTE\WUNDERGROUND
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_WUNDERGROUND_URL_BASE_REQUETE'))
	define('_RAINETTE_WUNDERGROUND_URL_BASE_REQUETE', 'http://api.wunderground.com/api');
if (!defined('_RAINETTE_WUNDERGROUND_URL_BASE_ICONE'))
	define('_RAINETTE_WUNDERGROUND_URL_BASE_ICONE', 'http://icons.wxug.com/i/c');
if (!defined('_RAINETTE_WUNDERGROUND_JOURS_PREVISIONS'))
	define('_RAINETTE_WUNDERGROUND_JOURS_PREVISIONS', 10);
if (!defined('_RAINETTE_WUNDERGROUND_SUFFIXE_METRIQUE'))
	define('_RAINETTE_WUNDERGROUND_SUFFIXE_METRIQUE', 'c:mb:km:kph|celsius:mm:kph');
if (!defined('_RAINETTE_WUNDERGROUND_SUFFIXE_STANDARD'))
	define('_RAINETTE_WUNDERGROUND_SUFFIXE_STANDARD', 'f:in:mi:mph|farenheit:in:mph');
if (!defined('_RAINETTE_WUNDERGROUND_LANGUE_DEFAUT'))
	define('_RAINETTE_WUNDERGROUND_LANGUE_DEFAUT', 'FR');


/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
 * unique de chargement des données météorologiques `charger_meteo()`.
 * PACKAGE SPIP\RAINETTE\WUNDERGROUND\API
 * ------------------------------------------------------------------------------------------------
 */


/**
 * @param $lieu
 * @param $mode
 * @return string
 */
function wunderground_service2cache($lieu, $mode) {
	include_spip('inc/config');
	$condition = lire_config('rainette/wunderground/condition');
	$langue = $GLOBALS['spip_lang'];

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'wunderground');
	$fichier_cache = $dir . str_replace(array(',', '+', '.', '/'), '-', $lieu) 
				   . "_" . $mode 
				   . ((($condition == 'wunderground') AND ($mode != 'infos')) ? '-' . $langue : '')
				   . ".txt";

	return $fichier_cache;
}

/**
 * @param $lieu
 * @param $mode
 * @return string
 */
function wunderground_service2url($lieu, $mode) {
	include_spip('inc/config');
	$cle = lire_config('rainette/wunderground/inscription');
	$format = lire_config('rainette/wunderground/format', 'json');

	// Determination de la demande
	if ($mode == 'infos') {
		$demande = 'geolookup';
	}
	else {
		$demande = ($mode == 'previsions') ? 'forecast10day/astronomy' : 'conditions';
	}

	// Identification et formatage du lieu
	$query = str_replace(array(' ', ','), array('', '/'), trim($lieu));
	$index = strpos($query, '/');
	if ($index !== false) {
		$ville = substr($query, 0, $index);
		$pays = substr($query, $index+1, strlen($query)-$index-1);
		$query = $pays . '/' . $ville;
	}

	// Identification de la langue du resume.
	// Le choix de la langue n'a d'interet que si on utilise le resume natif du service. Si ce n'est pas le cas
	// on ne la precise pas et on laisse l'API renvoyer la langue par defaut
	$condition = lire_config('rainette/wunderground/condition', 'wunderground');
	$code_langue = '';
	if ($condition == 'wunderground')
		$code_langue = langue2code_wunderground($GLOBALS['spip_lang']);

	$url = _RAINETTE_WUNDERGROUND_URL_BASE_REQUETE
		.  '/' . $cle
		.  '/' . $demande
		.  ($code_langue ? '/lang:' . $code_langue : '')
		.  '/q'
		.  '/' . $query . '.' . $format;

	return $url;
}


/**
 * Renvoie le système d'unité utilisé pour acquérir les données du service
 *
 * @return string
 */
function wunderground_service2unite() {
	include_spip('inc/config');

	// Identification du système d'unité
	$unite = lire_config('rainette/wunderground/unite', 'm');

	return $unite;
}


/**
 * @param $mode
 * @return mixed
 */
function wunderground_service2reload_time($mode) {
	static $reload = array('conditions' => 1800, 'previsions' => 7200);

	return $reload[$mode];
}

/**
 * @param $url
 * @return array
 */
function wunderground_url2flux($url) {
	// Déterminer le format d'échange pour aiguiller vers la bonne conversion
	include_spip('inc/config');
	$format = lire_config('rainette/wunderground/format', 'json');

	include_spip('inc/phraser');
	$flux = ($format == 'json') ? url2flux_json($url) : url2flux_xml($url, false);

	return $flux;
}


/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * @param array $flux
 * @return array
 */
function wunderground_flux2previsions($flux, $lieu) {
	// Identification des suffixes d'unite pour choisir le bon champ
	// -> wunderground fournit toujours les valeurs dans les deux systemes d'unites. Néanmois, la liste n'est pas
	//    la même pour les conditions et les prévisions
	include_spip('inc/config');
	$unites = (lire_config('rainette/wunderground/unite', 'm') == 'm')
		? _RAINETTE_WUNDERGROUND_SUFFIXE_METRIQUE
		: _RAINETTE_WUNDERGROUND_SUFFIXE_STANDARD;
	$unites = explode('|', $unites);
	$unites = explode(':', $unites[1]);

	// Identifier le format d'échange des données
	$format = lire_config('rainette/wunderground/format', 'json');

	// Construire le tableau standard des conditions météorologiques propres au service
	$tableau = ($format == 'json') ? json2previsions_wunderground($flux, $unites) : xml2previsions_wunderground($flux, $unites);

	// Compléter le tableau standard avec les états météorologiques calculés
	if ($tableau) {
		$condition = lire_config('rainette/wunderground/condition', 'wunderground');
		foreach ($tableau as $_index => $_prevision) {
			if ($_prevision[0]['code_meteo']
			AND $_prevision[0]['icon_meteo']
			AND isset($_prevision[0]['desc_meteo'])) {
				// Le mode jour/nuit n'est pas supporté par ce service.
				$tableau[$_index]['periode'] = 0; // jour

				// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
				if ($condition == 'wunderground') {
					// On affiche les prévisions natives fournies par le service.
					// Pour le resume, wwo ne fournit pas de traduction : on stocke donc le code meteo afin
					// de le traduire à partir des fichiers de langue SPIP.
					$theme = lire_config('rainette/wunderground/theme', 'a');
					$url = _RAINETTE_WUNDERGROUND_URL_BASE_ICONE . '/' . $theme . '/' . basename($_prevision[0]['icon_meteo']);
					$tableau[$_index][0]['icone']['code'] = $_prevision[0]['code_meteo'];
					$tableau[$_index][0]['icone']['url'] = copie_locale($url);
					$tableau[$_index][0]['resume'] = ucfirst($_prevision[0]['desc_meteo']);
				}
				else {
					// On affiche les conditions traduites dans le systeme weather.com
					$meteo = meteo_wunderground2weather($_prevision[0]['code_meteo'], $tableau[$_index]['periode']);
					$tableau[$_index][0]['icone'] = $meteo;
					$tableau[$_index][0]['resume'] = $meteo;
				}
			}
		}
	}

	// Traitement des erreurs de flux
	$index = count($tableau);
	$tableau[$index]['erreur'] = (!$tableau) ? 'chargement' : '';

	// Ajout des informations communes dans l'index adéquat
	$tableau[$index]['max_jours'] = _RAINETTE_WUNDERGROUND_JOURS_PREVISIONS;

	return $tableau;
}

function wunderground_flux2conditions($flux, $lieu) {
	// Correspondance des tendances de pression dans le système standard
	static $tendances = array('0' => 'steady', '+' => 'rising', '-' => 'falling');

	// Identification des suffixes d'unite pour choisir le bon champ
	// -> wunderground fournit toujours les valeurs dans les deux systemes d'unites. Néanmois, la liste n'est pas
	//    la même pour les conditions et les prévisions
	include_spip('inc/config');
	$unites = (lire_config('rainette/wunderground/unite', 'm') == 'm')
		? _RAINETTE_WUNDERGROUND_SUFFIXE_METRIQUE
		: _RAINETTE_WUNDERGROUND_SUFFIXE_STANDARD;
	$unites = explode('|', $unites);
	$suffixes = explode(':', $unites[0]);

	// Déterminer le format d'échange pour aiguiller vers la bonne conversion
	$format = lire_config('rainette/wunderground/format', 'json');

	// Construire le tableau standard des conditions météorologiques propres au service
	$tableau = ($format == 'json') ? json2conditions_wunderground($flux, $tendances, $suffixes) : xml2conditions_wunderground($flux, $tendances, $suffixes);

	// Compléter le tableau standard avec les états météorologiques calculés
	if ($tableau['code_meteo']
	AND $tableau['icon_meteo']
	AND isset($tableau['desc_meteo'])) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service (cas actuel) le nom du fichier icone commence par "nt_" pour la nuit.
		$icone = basename($tableau['icon_meteo']);
		if (strpos($icone, 'nt_') === false)
			$tableau['periode'] = 0; // jour
		else
			$tableau['periode'] = 1; // nuit

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		$condition = lire_config('rainette/wunderground/condition', 'wunderground');
		if ($condition == 'wunderground') {
			// On affiche les conditions natives fournies par le service.
			// Celles-ci etant deja traduites dans la bonne langue on stocke le texte exact retourne par l'API
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$theme = lire_config('rainette/wunderground/theme', 'a');
			$url = _RAINETTE_WUNDERGROUND_URL_BASE_ICONE . '/' . $theme
				 . '/' . ($tableau['periode'] == 1 ? 'nt_' : '') . $tableau['code_meteo'] . '.gif';
			$tableau['icone']['url'] = copie_locale($url);
			$tableau['resume'] = ucfirst($tableau['desc_meteo']);
		}
		else {
			// On affiche les conditions traduites dans le systeme weather.com
			// Pour le resume on stocke le code et non la traduction pour eviter de generer
			// un cache par langue comme pour le mode natif. La traduction est faite via les fichiers de langue
			$meteo = meteo_wunderground2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? 'chargement' : '';

	return $tableau;
}

function wunderground_flux2infos($flux, $lieu) {
	// Déterminer le format d'échange pour aiguiller vers la bonne conversion
	include_spip('inc/config');
	$format = lire_config('rainette/wunderground/format', 'json');

	// Construire le tableau standard des informations sur le lieu
	$tableau = ($format == 'json') ? json2infos_wunderground($flux) : xml2infos_wunderground($flux);

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? 'chargement' : '';

	return $tableau;
}

function wunderground_service2credits() {

	$credits = array('titre' => '');
	$credits['lien'] = 'http://www.wunderground.com/';
	$credits['logo'] = 'wunderground-126.png';

	return $credits;
}


/**
 * -----------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont permettent le décodage des données météorologiques reçues au
 * format XML. Ce sont des sous-fonctions internes appelées uniquement par les fonctions de l'API.
 * PACKAGE SPIP\RAINETTE\WUNDERGROUND\XML
 * -----------------------------------------------------------------------------------------------
 */

function xml2previsions_wunderground($flux) {
	$tableau = array();

	if (isset($flux['children']['forecast'][0]['children']['simpleforecast'][0]['children']['forecastdays'][0]['children']['forecastday'])) {
		$previsions = $flux['children']['forecast'][0]['children']['simpleforecast'][0]['children']['forecastdays'][0]['children']['forecastday'];
		$maintenant = time();

		if ($previsions) {
			foreach ($previsions as $_index => $_prevision) {
				if ($_prevision) {
					$_prevision = $_prevision['children'];

					// Index du jour et date du jour
					$tableau[$_index]['index'] = $_index;
					$tableau[$_index]['date'] = (isset($_prevision['date'][0]['children']['epoch']))
						? date($_prevision['date'][0]['children']['epoch'][0]['text'])
						: date('Y-m-d', $maintenant + 24*3600*$_index);

					// Date complete des lever/coucher du soleil
					$tableau[$_index]['lever_soleil'] = NULL;
					$tableau[$_index]['coucher_soleil'] = NULL;

					// Previsions du jour
					list($ut, $ur, $uv) = $unites;
					$tableau[$_index][0]['temperature_max'] = (isset($_prevision['high'][0]['children'])) ? floatval($_prevision['high'][0]['children'][$ut][0]['text']) : '';
					$tableau[$_index][0]['temperature_min'] = (isset($_prevision['low'][0]['children'])) ? floatval($_prevision['low'][0]['children'][$ut][0]['text']) : '';
					$tableau[$_index][0]['vitesse_vent'] = (isset($_prevision['avewind'][0]['children'])) ? floatval($_prevision['avewind'][0]['children'][$uv][0]['text']) : '';
					$tableau[$_index][0]['angle_vent'] = (isset($_prevision['avewind'][0]['children'])) ? intval($_prevision['avewind'][0]['children']['degrees'][0]['text']) : '';
					// La documentation indique que les directions uniques sont fournies sous forme de texte comme North
					// alors que les autres sont des acronymes. En outre, la valeur semble être traduite
					// --> Le mieux est donc de convertir à partir de l'angle
					include_spip('inc/convertir');
					$tableau[$_index][0]['direction_vent'] = (isset($_prevision['avewind'][0]['children'])) ? angle2direction($tableau[$_index][0]['angle_vent']) : '';

					$tableau[$_index][0]['risque_precipitation'] = (isset($_prevision['pop'])) ? intval($_prevision['pop'][0]['text']) : '';
					$tableau[$_index][0]['precipitation'] = (isset($_prevision['qpf_allday'][0]['children'])) ? floatval($_prevision['qpf_allday'][0]['children'][$ur][0]['text']) : '';
					$tableau[$_index][0]['humidite'] = (isset($_prevision['avehumidity'])) ? intval($_prevision['avehumidity'][0]['text']) : '';

					$tableau[$_index][0]['code_meteo'] = (isset($_prevision['icon'])) ? $_prevision['icon'][0]['text'] : '';
					$tableau[$_index][0]['icon_meteo'] = (isset($_prevision['icon_url'])) ? $_prevision['icon_url'][0]['text'] : '';
					$tableau[$_index][0]['desc_meteo'] = (isset($_prevision['conditions'])) ? $_prevision['conditions'][0]['text'] : '';

					// Previsions de la nuit si elle existe
					$tableau[$_index][1] = NULL;
				}
			}
		}
	}

	return $tableau;
}


function xml2conditions_wunderground($flux, $tendances, $suffixes) {
	$tableau = array();

	if (isset($flux['children']['current_observation'][0]['children'])) {
		$conditions = $flux['children']['current_observation'][0]['children'];

		// Date d'observation
		$date_maj = (isset($conditions['observation_epoch'])) ? intval($conditions['observation_epoch'][0]['text']) : 0;
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		$tableau['station'] = (isset($conditions['observation_location']))
			? trim($conditions['observation_location'][0]['children']['full'][0]['text'], ',')
			: '';

		// Liste des conditions meteo extraites dans le systeme demande
		list($ut, $up, $ud, $uv) = $suffixes;
		$tableau['vitesse_vent'] = (isset($conditions['wind_'.$uv])) ? floatval($conditions['wind_'.$uv][0]['text']) : '';
		$tableau['angle_vent'] = (isset($conditions['wind_degrees'])) ? intval($conditions['wind_degrees'][0]['text']) : '';
		// La documentation indique que les directions uniques sont fournies sous forme de texte comme North
		// alors que les autres sont des acronymes. En outre, la valeur semble être traduite
		// --> Le mieux est donc de convertir à partir de l'angle
		include_spip('inc/convertir');
		$tableau['direction_vent'] = (isset($conditions['wind_degrees'])) ? angle2direction($tableau['angle_vent']) : '';

		$tableau['temperature_reelle'] = (isset($conditions['temp_'.$ut])) ? floatval($conditions['temp_'.$ut][0]['text']) : '';
		$tableau['temperature_ressentie'] = (isset($conditions['feelslike_'.$ut])) ? floatval($conditions['feelslike_'.$ut][0]['text']) : '';

		$tableau['humidite'] = (isset($conditions['relative_humidity'])) ? intval($conditions['relative_humidity'][0]['text']) : '';
		$tableau['point_rosee'] = (isset($conditions['dewpoint_'.$ut])) ? intval($conditions['dewpoint_'.$ut][0]['text']) : '';

		$tableau['pression'] = (isset($conditions['pressure_'.$up])) ? floatval($conditions['pressure_'.$up][0]['text']) : '';
		$tableau['tendance_pression'] = (isset($conditions['pressure_trend']) AND array_key_exists($conditions['pressure_trend'][0]['text'], $tendances))
					? $tendances[$conditions['pressure_trend'][0]['text']]
					: '';

		$tableau['visibilite'] = (isset($conditions['visibility_'.$ud])) ? floatval($conditions['visibility_'.$ud][0]['text']) : '';

		// Code meteo, resume et icone natifs au service
		$tableau['code_meteo'] = (isset($conditions['icon'])) ? $conditions['icon'][0]['text'] : '';
		$tableau['icon_meteo'] = (isset($conditions['icon_url'])) ? $conditions['icon_url'][0]['text'] : '';
		$tableau['desc_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'][0]['text'] : '';
	}

	return $tableau;
}

function xml2infos_wunderground($flux) {
	$tableau = array();

	if (isset($flux['children']['location'][0]['children'])) {
		$infos = $flux['children']['location'][0]['children'];

		if (isset($infos['city'])) {
			$tableau['ville'] = $infos['city'][0]['text'];
			$tableau['ville'] .= (isset($infos['country_name'])) ? ', ' . $infos['country_name'][0]['text'] : '';
		}
		$tableau['region'] = NULL;

		$tableau['longitude'] = (isset($infos['lon'])) ? floatval($infos['lon'][0]['text']) : '';
		$tableau['latitude'] = (isset($infos['lat'])) ? floatval($infos['lat'][0]['text']) : '';

		$tableau['population'] = NULL;
	}

	return $tableau;
}

/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont permettent le décodage des données météorologiques reçues au
 * format JSON. Ce sont des sous-fonctions internes appelées uniquement par les fonctions de l'API.
 * PACKAGE SPIP\RAINETTE\WUNDERGROUND\JSON
 * ------------------------------------------------------------------------------------------------
 */

function json2previsions_wunderground($flux, $unites) {
	$tableau = array();

	if (isset($flux['forecast']['simpleforecast']['forecastday'])) {
		$previsions = $flux['forecast']['simpleforecast']['forecastday'];
		$maintenant = time();

		if ($previsions) {
			foreach ($previsions as $_index => $_prevision) {
				if ($_prevision) {
					// Index du jour et date du jour
					$tableau[$_index]['index'] = $_index;
					$tableau[$_index]['date'] = (isset($_prevision['date']))
						? date($_prevision['date']['epoch'])
						: date('Y-m-d', $maintenant + 24*3600*$_index);

					// Date complete des lever/coucher du soleil
					$tableau[$_index]['lever_soleil'] = NULL;
					$tableau[$_index]['coucher_soleil'] = NULL;

					// Previsions du jour
					list($ut, $ur, $uv) = $unites;
					$tableau[$_index][0]['temperature_max'] = (isset($_prevision['high'])) ? floatval($_prevision['high'][$ut]) : '';
					$tableau[$_index][0]['temperature_min'] = (isset($_prevision['low'])) ? floatval($_prevision['low'][$ut]) : '';
					$tableau[$_index][0]['vitesse_vent'] = (isset($_prevision['avewind'])) ? floatval($_prevision['avewind'][$uv]) : '';
					$tableau[$_index][0]['angle_vent'] = (isset($_prevision['avewind'])) ? intval($_prevision['avewind']['degrees']) : '';
					// La documentation indique que les directions uniques sont fournies sous forme de texte comme North
					// alors que les autres sont des acronymes. En outre, la valeur semble être traduite
					// --> Le mieux est donc de convertir à partir de l'angle
					include_spip('inc/convertir');
					$tableau[$_index][0]['direction_vent'] = (isset($_prevision['avewind'])) ? angle2direction($tableau[$_index][0]['angle_vent']) : '';

					$tableau[$_index][0]['risque_precipitation'] = (isset($_prevision['pop'])) ? intval($_prevision['pop']) : '';
					$tableau[$_index][0]['precipitation'] = (isset($_prevision['qpf_allday'])) ? floatval($_prevision['qpf_allday'][$ur]) : '';
					$tableau[$_index][0]['humidite'] = (isset($_prevision['avehumidity'])) ? intval($_prevision['avehumidity']) : '';

					$tableau[$_index][0]['code_meteo'] = (isset($_prevision['icon'])) ? $_prevision['icon'] : '';
					$tableau[$_index][0]['icon_meteo'] = (isset($_prevision['icon_url'])) ? $_prevision['icon_url'] : '';
					$tableau[$_index][0]['desc_meteo'] = (isset($_prevision['conditions'])) ? $_prevision['conditions'] : '';

					// Previsions de la nuit si elle existe
					$tableau[$_index][1] = NULL;
				}
			}
		}
	}

	return $tableau;
}


function json2conditions_wunderground($flux, $tendances, $suffixes) {
	$tableau = array();

	if (isset($flux['current_observation'])) {
		$conditions = $flux['current_observation'];

		// Date d'observation
		$date_maj = (isset($conditions['observation_epoch'])) ? intval($conditions['observation_epoch']) : 0;
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		$tableau['station'] = (isset($conditions['observation_location']['full']))
			? $conditions['observation_location']['full']
			: '';

		// Liste des conditions meteo extraites dans le systeme demande
		list($ut, $up, $ud, $uv) = $suffixes;
		$tableau['vitesse_vent'] = (isset($conditions['wind_'.$uv])) ? floatval($conditions['wind_'.$uv]) : '';
		$tableau['angle_vent'] = (isset($conditions['wind_degrees'])) ? intval($conditions['wind_degrees']) : '';
		// La documentation indique que les directions uniques sont fournies sous forme de texte comme North
		// alors que les autres sont des acronymes. En outre, la valeur semble être traduite
		// --> Le mieux est donc de convertir à partir de l'angle
		include_spip('inc/convertir');
		$tableau['direction_vent'] = (isset($conditions['wind_degrees'])) ? angle2direction($tableau['angle_vent']) : '';

		$tableau['temperature_reelle'] = (isset($conditions['temp_'.$ut])) ? floatval($conditions['temp_'.$ut]) : '';
		$tableau['temperature_ressentie'] = (isset($conditions['feelslike_'.$ut])) ? floatval($conditions['feelslike_'.$ut]) : '';

		$tableau['humidite'] = (isset($conditions['relative_humidity'])) ? intval($conditions['relative_humidity']) : '';
		$tableau['point_rosee'] = (isset($conditions['dewpoint_'.$ut])) ? intval($conditions['dewpoint_'.$ut]) : '';

		$tableau['pression'] = (isset($conditions['pressure_'.$up])) ? floatval($conditions['pressure_'.$up]) : '';
		$tableau['tendance_pression'] = (isset($conditions['pressure_trend']) AND array_key_exists($conditions['pressure_trend'], $tendances))
			? $tendances[$conditions['pressure_trend']]
			: '';

		$tableau['visibilite'] = (isset($conditions['visibility_'.$ud])) ? floatval($conditions['visibility_'.$ud]) : '';

		// Code meteo, resume et icone natifs au service
		$tableau['code_meteo'] = (isset($conditions['icon'])) ? $conditions['icon'] : '';
		$tableau['icon_meteo'] = (isset($conditions['icon_url'])) ? $conditions['icon_url'] : '';
		$tableau['desc_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'] : '';
	}

	return $tableau;
}

function json2infos_wunderground($flux) {
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($flux['location'])) {
		$infos = $flux['location'];

		if (isset($infos['city'])) {
			$tableau['ville'] = $infos['city'];
			$tableau['ville'] .= (isset($infos['country_name'])) ? ', ' . $infos['country_name'] : '';
		}
		$tableau['region'] = NULL;

		$tableau['longitude'] = (isset($infos['lon'])) ? floatval($infos['lon']) : '';
		$tableau['latitude'] = (isset($infos['lat'])) ? floatval($infos['lat']) : '';

		$tableau['population'] = NULL;
	}

	return $tableau;
}


/**
 * ---------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont des utilitaires utilisés uniquement appelées par les fonctions
 * de l'API.
 * PACKAGE SPIP\RAINETTE\WUNDERGROUND\OUTILS
 * ---------------------------------------------------------------------------------------------
 */


/**
 * @internal
 *
 * @link http://plugins.trac.wordpress.org/browser/weather-and-weather-forecast-widget/trunk/gg_funx_.php
 * Transcodage issu du plugin Wordpress weather forecast.
 *
 * @param string $meteo
 * @param int $periode
 * @return string
 */
function meteo_wunderground2weather($meteo, $periode=0) {
	static $wunderground2weather = array(
							'chanceflurries'=> array(41,46),
							'chancerain'=> array(39,45),
							'chancesleet'=> array(39,45),
							'chancesleet'=> array(41,46),
							'chancesnow'=> array(41,46),
							'chancetstorms'=> array(38,47),
							'clear'=> array(32,31),
							'cloudy'=> array(26,26),
							'flurries'=> array(15,15),
							'fog'=> array(20,20),
							'hazy'=> array(21,21),
							'mostlycloudy'=> array(28,27),
							'mostlysunny'=> array(34,33),
							'partlycloudy'=> array(30,29),
							'partlysunny'=> array(28,27),
							'sleet'=> array(5,5),
							'rain'=> array(11,11),
							'sleet'=> array(5,5),
							'snow'=> array(16,16),
							'sunny'=> array(32,31),
							'tstorms'=> array(4,4),
							'thunderstorms'=> array(4,4),
							'unknown'=> array(4,4),
							'cloudy'=> array(26,26),
							'scatteredclouds'=> array(30,29),
							'overcast'=> array(26,26));

	$icone = 'na';
	$meteo = strtolower($meteo);
	if (array_key_exists($meteo,  $wunderground2weather))
		$icone = strval($wunderground2weather[$meteo][$periode]);
	return $icone;
}

function langue2code_wunderground($langue) {
	static $langue2wunderground = array(
		'aa' => array('', ''), 					// afar
		'ab' => array('', ''), 					// abkhaze
		'af' => array('AF', ''), 				// afrikaans
		'am' => array('', ''), 					// amharique
		'an' => array('', 'SP'),				// aragonais
		'ar' => array('AR', ''), 				// arabe
		'as' => array('', ''), 					// assamais
		'ast' => array('', 'SP'), 				// asturien - iso 639-2
		'ay' => array('', ''), 					// aymara
		'az' => array('AZ', ''), 				// azeri
		'ba' => array('', ''),					// bashkir
		'be' => array('BY', ''), 				// bielorusse
		'ber_tam' => array('', ''),				// berbère
		'ber_tam_tfng' => array('', ''),		// berbère tifinagh
		'bg' => array('BU', ''), 				// bulgare
		'bh' => array('', ''),					// langues biharis
		'bi' => array('', ''),					// bichlamar
		'bm' => array('', ''),					// bambara
		'bn' => array('', ''),					// bengali
		'bo' => array('', ''),					// tibétain
		'br' => array('', 'FR'),				// breton
		'bs' => array('', ''),					// bosniaque
		'ca' => array('CA', ''),				// catalan
		'co' => array('', 'FR'),				// corse
		'cpf' => array('', 'FR'), 				// créole réunionais
		'cpf_dom' => array('', 'FR'), 			// créole ???
		'cpf_hat' => array('HT', ''), 			// créole haïtien
		'cs' => array('CZ', ''),				// tchèque
		'cy' => array('CY', ''),				// gallois
		'da' => array('DK', ''),				// danois
		'de' => array('DL', ''),				// allemand
		'dz' => array('', ''),					// dzongkha
		'el' => array('GR', ''),				// grec moderne
		'en' => array('EN', ''),				// anglais
		'en_hx' => array('', 'EN'),				// anglais hacker
		'en_sm' => array('', 'EN'),				// anglais smurf
		'eo' => array('EO', ''),				// esperanto
		'es' => array('SP', ''),				// espagnol
		'es_co' => array('', 'SP'),				// espagnol colombien
		'es_mx_pop' => array('', 'SP'),			// espagnol mexicain
		'et' => array('ET', ''),				// estonien
		'eu' => array('EU', ''),				// basque
		'fa' => array('FA', ''),				// persan (farsi)
		'ff' => array('', ''),					// peul
		'fi' => array('FI', ''),				// finnois
		'fj' => array('', 'EN'),				// fidjien
		'fo' => array('', 'DK'),				// féroïen
		'fon' => array('', ''),					// fon
		'fr' => array('FR', ''),				// français
		'fr_sc' => array('', 'FR'),				// français schtroumpf
		'fr_lpc' => array('', 'FR'),			// français langue parlée
		'fr_lsf' => array('', 'FR'),			// français langue des signes
		'fr_spl' => array('', 'FR'),			// français simplifié
		'fr_tu' => array('', 'FR'),				// français copain
		'fy' => array('', 'DL'),				// frison occidental
		'ga' => array('IR', ''),				// irlandais
		'gd' => array('', 'EN'),				// gaélique écossais
		'gl' => array('GZ', ''),				// galicien
		'gn' => array('', ''),					// guarani
		'grc' => array('', 'GR'),				// grec ancien
		'gu' => array('GU', ''),				// goudjrati
		'ha' => array('', ''),					// haoussa
		'hac' => array('', 'KU'), 				// Kurdish-Horami
		'hbo' => array('', 'IL'),				// hebreu classique ou biblique
		'he' => array('IL', ''),				// hébreu
		'hi' => array('HI', ''),				// hindi
		'hr' => array('CR', ''),				// croate
		'hu' => array('HU', ''),	 			// hongrois
		'hy' => array('HY', ''), 				// armenien
		'ia' => array('', ''),					// interlingua (langue auxiliaire internationale)
		'id' => array('ID', ''),				// indonésien
		'ie' => array('', ''),					// interlingue
		'ik' => array('', ''),					// inupiaq
		'is' => array('IS', ''),				// islandais
		'it' => array('IT', ''),				// italien
		'it_fem' => array('', 'IT'),			// italien féminin
		'iu' => array('', ''),					// inuktitut
		'ja' => array('JP', ''),				// japonais
		'jv' => array('JW', ''),				// javanais
		'ka' => array('KA', ''),				// géorgien
		'kk' => array('', ''),					// kazakh
		'kl' => array('', 'DK'),				// groenlandais
		'km' => array('KM', ''),				// khmer central
		'kn' => array('', ''),					// Kannada
		'ko' => array('KR', ''),				// coréen
		'ks' => array('', ''),					// kashmiri
		'ku' => array('KU', ''),				// kurde
		'ky' => array('', ''),					// kirghiz
		'la' => array('LA', ''),				// latin
		'lb' => array('', 'FR'),				// luxembourgeois
		'ln' => array('', ''),					// lingala
		'lo' => array('', ''), 					// lao
		'lt' => array('LT', ''),				// lituanien
		'lu' => array('', ''),					// luba-katanga
		'lv' => array('LV', ''),				// letton
		'man' => array('GM', ''),				// mandingue
		'mfv' => array('', ''), 				// manjaque - iso-639-3
		'mg' => array('', ''),					// malgache
		'mi' => array('MI', ''),				// maori
		'mk' => array('MK', ''),				// macédonien
		'ml' => array('', ''),					// malayalam
		'mn' => array('MN', ''),				// mongol
		'mo' => array('', 'RO'),				// moldave ??? normalement c'est ro comme le roumain
		'mos' => array('', ''),					// moré - iso 639-2
		'mr' => array('MR', ''),				// marathe
		'ms' => array('', ''),					// malais
		'mt' => array('MT', ''),				// maltais
		'my' => array('MY', ''),				// birman
		'na' => array('', ''),					// nauruan
		'nap' => array('', 'IT'),				// napolitain - iso 639-2
		'ne' => array('', ''),					// népalais
		'nqo' => array('', ''), 				// n’ko - iso 639-3
		'nl' => array('NL', ''),				// néerlandais
		'no' => array('NO', ''),				// norvégien
		'nb' => array('', 'NO'),				// norvégien bokmål
		'nn' => array('', 'NO'),				// norvégien nynorsk
		'oc' => array('OC', ''),				// occitan
		'oc_lnc' => array('', 'OC'),			// occitan languedocien
		'oc_ni' => array('', 'OC'),				// occitan niçard
		'oc_ni_la' => array('', 'OC'),			// occitan niçard
		'oc_prv' => array('', 'OC'),			// occitan provençal
		'oc_gsc' => array('', 'OC'),			// occitan gascon
		'oc_lms' => array('', 'OC'),			// occitan limousin
		'oc_auv' => array('', 'OC'),			// occitan auvergnat
		'oc_va' => array('', 'OC'),				// occitan vivaro-alpin
		'om' => array('', ''),					// galla
		'or' => array('', ''),					// oriya
		'pa' => array('PA', ''),				// pendjabi
		'pbb' => array('', ''),					// Nasa Yuwe (páez) - iso 639-3
		'pl' => array('PL', ''),				// polonais
		'ps' => array('PS', ''),				// pachto
		'pt' => array('BR', ''),				// portugais
		'pt_br' => array('', 'BR'),				// portugais brésilien
		'qu' => array('', ''),					// quechua
		'rm' => array('', ''),					// romanche
		'rn' => array('', ''),					// rundi
		'ro' => array('RO', ''),				// roumain
		'roa' => array('chti', ''),				// langues romanes (ch'ti) - iso 639-2
		'ru' => array('RU', ''),				// russe
		'rw' => array('', ''),					// rwanda
		'sa' => array('', ''),					// sanskrit
		'sc' => array('', 'IT'),				// sarde
		'scn' => array('', 'IT'),				// sicilien - iso 639-2
		'sd' => array('', ''),					// sindhi
		'sg' => array('', ''),					// sango
		'sh' => array('', 'SR'),				// serbo-croate
		'sh_latn' => array('', 'SR'),			// serbo-croate latin
		'sh_cyrl' => array('', 'SR'),			// serbo-croate cyrillique
		'si' => array('', ''),					// singhalais
		'sk' => array('SK', ''),				// slovaque
		'sl' => array('SL', ''),				// slovène
		'sm' => array('', ''),					// samoan
		'sn' => array('', ''),					// shona
		'so' => array('', ''),					// somali
		'sq' => array('AL', ''), 				// albanais
		'sr' => array('SR', ''),				// serbe
		'src' => array('', 'IT'), 				// sarde logoudorien - iso 639-3
		'sro' => array('', 'IT'), 				// sarde campidanien - iso 639-3
		'ss' => array('', ''),					// swati
		'st' => array('', ''),					// sotho du Sud
		'su' => array('', ''),					// soundanais
		'sv' => array('SW', ''),				// suédois
		'sw' => array('SI', ''),				// swahili
		'ta' => array('', ''), 					// tamoul
		'te' => array('', ''),					// télougou
		'tg' => array('', ''),					// tadjik
		'th' => array('TH', ''),				// thaï
		'ti' => array('', ''),					// tigrigna
		'tk' => array('TK', ''),				// turkmène
		'tl' => array('TL', ''),				// tagalog
		'tn' => array('', ''),					// tswana
		'to' => array('', ''),					// tongan (Îles Tonga)
		'tr' => array('TR', ''),				// turc
		'ts' => array('', ''),					// tsonga
		'tt' => array('TT', ''),				// tatar
		'tw' => array('', ''),					// twi
		'ty' => array('', 'FR'),			 	// tahitien
		'ug' => array('', ''),					// ouïgour
		'uk' => array('UA', ''),				// ukrainien
		'ur' => array('', ''),					// ourdou
		'uz' => array('UZ', ''),				// ouszbek
		'vi' => array('VU', ''),				// vietnamien
		'vo' => array('', ''),					// volapük
		'wa' => array('', 'FR'),				// wallon
		'wo' => array('SN', ''),				// wolof
		'xh' => array('', ''),					// xhosa
		'yi' => array('YI', ''),				// yiddish
		'yo' => array('', ''),					// yoruba
		'za' => array('', 'CN'),				// zhuang
		'zh' => array('CN', ''), 				// chinois (ecriture simplifiee)
		'zh_tw' => array('TW', ''), 			// chinois taiwan (ecriture traditionnelle)
		'zu' => array('', '')					// zoulou
	);

	$code = _RAINETTE_WUNDERGROUND_LANGUE_DEFAUT;
	if (array_key_exists($langue,  $langue2wunderground)) {
		if ($c0 = $langue2wunderground[$langue][0])
			$code = strtoupper($c0);
		else
			$code = strtoupper($langue2wunderground[$langue][1]);
	}

	return $code;
}

?>