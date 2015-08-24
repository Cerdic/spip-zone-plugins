<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service World Weather Online (wwo).
 * Ce service fournit des données au format XML ou JSON.
 *
 * @package SPIP\RAINETTE\WWO
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_WWO_URL_BASE'))
	define('_RAINETTE_WWO_URL_BASE', 'http://api.worldweatheronline.com/free/v2/weather.ashx');
if (!defined('_RAINETTE_WWO_JOURS_PREVISIONS'))
	define('_RAINETTE_WWO_JOURS_PREVISIONS', 5);
if (!defined('_RAINETTE_WWO_SUFFIXE_METRIQUE'))
	define('_RAINETTE_WWO_SUFFIXE_METRIQUE', 'c:mm:kmph');
if (!defined('_RAINETTE_WWO_SUFFIXE_STANDARD'))
	define('_RAINETTE_WWO_SUFFIXE_STANDARD', 'f:in:miles');

// Configuration des données fournies par le service wwo pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['conditions'] = array(
    'xml' => array(
        'base'	=> array('children', 'current_condition', 0, 'children'),
       	'donnees'	=> array(
			// Données d'observation
			'derniere_maj'			=> array('cle' => array('localobsdatetime', 0, 'text'), 'suffixe_unite' => ''),
			'station'				=> array('cle' => array(), 'suffixe_unite' => ''),
			// Températures
			'temperature_reelle'	=> array('cle' => array('temp_', 0, 'text'), 'suffixe_unite' => array('m' => 'c', 's' => 'f')),
			'temperature_ressentie'	=> array('cle' => array('feelslike', 0, 'text'), 'suffixe_unite' => array('m' => 'c', 's' => 'f')),
			// Données anémométriques
			'vitesse_vent'			=> array('cle' => array('windspeed', 0, 'text'), 'suffixe_unite' => array('m' => 'kmph', 's' => 'miles')),
			'angle_vent'			=> array('cle' => array('winddirdegree', 0, 'text'), 'suffixe_unite' => ''),
			'direction_vent'		=> array('cle' => array('winddir16point', 0, 'text'), 'suffixe_unite' => ''),
			// Données atmosphériques : risque_uv est calculé
			'precipitation'			=> array('cle' => array('precipmm', 0, 'text'), 'suffixe_unite' => ''),
			'humidite'				=> array('cle' => array('humidity', 0, 'text'), 'suffixe_unite' => ''),
			'point_rosee'			=> array('cle' => array(), 'suffixe_unite' => ''),
			'pression'				=> array('cle' => array('pressure', 0, 'text'), 'suffixe_unite' => ''),
			'tendance_pression'		=> array('cle' => array(), 'suffixe_unite' => ''),
			'visibilite'			=> array('cle' => array('visibility', 0, 'text'), 'suffixe_unite' => ''),
			'indice_uv'				=> array('cle' => array(), 'suffixe_unite' => ''),
			// Etats météorologiques natifs
			'code_meteo'			=> array('cle' => array('weathercode', 0, 'text'), 'suffixe_unite' => ''),
			'icon_meteo'			=> array('cle' => array('weathericonurl', 0, 'text'), 'suffixe_unite' => ''),
			'desc_meteo'			=> array('cle' => array('weatherdesc', 0, 'text'), 'suffixe_unite' => ''),
			// Etats météorologiques calculés : icone, resume, periode sont calculés
       	),
    ),
    'json' => array(
        'base'	=> array('data', 'current_condition', 0),
       	'donnees'	=> array(
			// Données d'observation
			'derniere_maj'			=> array('cle' => array('localObsDateTime'), 'suffixe_unite' => ''),
			'station'				=> array('cle' => array(), 'suffixe_unite' => ''),
			// Températures
			'temperature_reelle'	=> array('cle' => array('temp_'), 'suffixe_unite' => array('m' => 'C', 's' => 'F')),
			'temperature_ressentie'	=> array('cle' => array('FeelsLike'), 'suffixe_unite' => array('m' => 'C', 's' => 'F')),
			// Données anémométriques
			'vitesse_vent'			=> array('cle' => array('windspeed'), 'suffixe_unite' => array('m' => 'kmph', 's' => 'miles')),
			'angle_vent'			=> array('cle' => array('winddirDegree'), 'suffixe_unite' => ''),
			'direction_vent'		=> array('cle' => array('winddir16Point'), 'suffixe_unite' => ''),
			// Données atmosphériques : risque_uv est calculé
			'precipitation'			=> array('cle' => array('precipMM'), 'suffixe_unite' => ''),
			'humidite'				=> array('cle' => array('humidity'), 'suffixe_unite' => ''),
			'point_rosee'			=> array('cle' => array(), 'suffixe_unite' => ''),
			'pression'				=> array('cle' => array('pressure'), 'suffixe_unite' => ''),
			'tendance_pression'		=> array('cle' => array(), 'suffixe_unite' => ''),
			'visibilite'			=> array('cle' => array('visibility'), 'suffixe_unite' => ''),
			'indice_uv'				=> array('cle' => array(), 'suffixe_unite' => ''),
			// Etats météorologiques natifs
			'code_meteo'			=> array('cle' => array('weatherCode'), 'suffixe_unite' => ''),
			'icon_meteo'			=> array('cle' => array('weatherIconUrl', 0, 'value'), 'suffixe_unite' => ''),
			'desc_meteo'			=> array('cle' => array('weatherDesc', 0, 'value'), 'suffixe_unite' => ''),
			// Etats météorologiques calculés : icone, resume, periode sont calculés
       	),
    ),
);


// Configuration des données fournies par le service wwo pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['previsions'] = array(
    'xml' => array(
        'base'	=> array('children', 'weather'),
       	'donnees'	=> array(
			// Données d'observation : l'index est calculé
			'date'					=> array('cle' => array('date', 0, 'text'), 'suffixe_unite' => ''),
			// Données astronomiques
			'lever_soleil'			=> array('cle' => array(), 'suffixe_unite' => ''),
			'coucher_soleil'		=> array('cle' => array(), 'suffixe_unite' => ''),
			// Températures
			'temperature_max'		=> array('cle' => array('tempmax', 0, 'text'), 'suffixe_unite' => array('m' => 'c', 's' => 'f')),
			'temperature_min'		=> array('cle' => array('tempmin', 0, 'text'), 'suffixe_unite' => array('m' => 'c', 's' => 'f')),
			// Données anémométriques
			'vitesse_vent'			=> array('cle' => array('windspeed', 0, 'text'), 'suffixe_unite' => array('m' => 'kmph', 's' => 'miles')),
			'angle_vent'			=> array('cle' => array('winddirdegree', 0, 'text'), 'suffixe_unite' => ''),
			'direction_vent'		=> array('cle' => array('winddir16point', 0, 'text'), 'suffixe_unite' => ''),
			// Données atmosphériques : risque_uv est calculé
			'risque_precipitation'	=> array('cle' => array(), 'suffixe_unite' => ''),
			'precipitation'			=> array('cle' => array('precipmm', 0, 'text'), 'suffixe_unite' => ''),
			'humidite'				=> array('cle' => array(), 'suffixe_unite' => ''),
			'pression'				=> array('cle' => array('pressure', 0, 'text'), 'suffixe_unite' => ''),
			'indice_uv'				=> array('cle' => array(), 'suffixe_unite' => ''),
			// Etats météorologiques natifs
			'code_meteo'			=> array('cle' => array('weathercode', 0, 'text'), 'suffixe_unite' => ''),
			'icon_meteo'			=> array('cle' => array('weathericonurl', 0, 'text'), 'suffixe_unite' => ''),
			'desc_meteo'			=> array('cle' => array('weatherdesc', 0, 'text'), 'suffixe_unite' => ''),
			// Etats météorologiques calculés : icone, resume, periode sont calculés
       	),
    ),
    'json' => array(
        'base'	=> array('data', 'current_condition', 0),
       	'donnees'	=> array(
			// Données d'observation
			'derniere_maj'			=> array('cle' => array('localObsDateTime'), 'suffixe_unite' => ''),
			'station'				=> array('cle' => array(), 'suffixe_unite' => ''),
			// Températures
			'temperature_reelle'	=> array('cle' => array('temp_'), 'suffixe_unite' => array('m' => 'C', 's' => 'F')),
			'temperature_ressentie'	=> array('cle' => array('FeelsLike'), 'suffixe_unite' => array('m' => 'C', 's' => 'F')),
			// Données anémométriques
			'vitesse_vent'			=> array('cle' => array('windspeed'), 'suffixe_unite' => array('m' => 'kmph', 's' => 'miles')),
			'angle_vent'			=> array('cle' => array('winddirDegree'), 'suffixe_unite' => ''),
			'direction_vent'		=> array('cle' => array('winddir16Point'), 'suffixe_unite' => ''),
			// Données atmosphériques : risque_uv est calculé
			'precipitation'			=> array('cle' => array('precipMM'), 'suffixe_unite' => ''),
			'humidite'				=> array('cle' => array('humidity'), 'suffixe_unite' => ''),
			'point_rosee'			=> array('cle' => array(), 'suffixe_unite' => ''),
			'pression'				=> array('cle' => array('pressure'), 'suffixe_unite' => ''),
			'tendance_pression'		=> array('cle' => array(), 'suffixe_unite' => ''),
			'visibilite'			=> array('cle' => array('visibility'), 'suffixe_unite' => ''),
			'indice_uv'				=> array('cle' => array(), 'suffixe_unite' => ''),
			// Etats météorologiques natifs
			'code_meteo'			=> array('cle' => array('weatherCode'), 'suffixe_unite' => ''),
			'icon_meteo'			=> array('cle' => array('weatherIconUrl', 0, 'value'), 'suffixe_unite' => ''),
			'desc_meteo'			=> array('cle' => array('weatherDesc', 0, 'value'), 'suffixe_unite' => ''),
			// Etats météorologiques calculés : icone, resume, periode sont calculés
       	),
    ),
);

// Configuration des données fournies par le service wwo pour le mode 'infos'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['infos'] = array(
    'xml' => array(
        'base'	=> array('children', 'nearest_area', 0, 'children'),
       	'donnees'	=> array(
       		// Lieu
       		'ville'				=> array('cle' => array('areaname+country', 0, 'text'), 'suffixe_unite' => ''),
       		'region'			=> array('cle' => array('region', 0, 'text'), 'suffixe_unite' => ''),
       		// Coordonnées
       		'longitude'			=> array('cle' => array('longitude', 0, 'text'), 'suffixe_unite' => ''),
       		'latitude'			=> array('cle' => array('latitude', 0, 'text'), 'suffixe_unite' => ''),
       		// Données démographiques
       		'population'		=> array('cle' => array('population', 0, 'text'), 'suffixe_unite' => ''),
       		// Informations complémentaires : aucune configuration car ce sont des données calculées
       	),
    ),
    'json' => array(
        'base'	=> array('data', 'nearest_area', 0),
       	'donnees'	=> array(
       		// Lieu
            'ville'				=> array('cle' => array('areaName+country', 0, 'value'), 'suffixe_unite' => ''),
            'region'			=> array('cle' => array('region', 0, 'value'), 'suffixe_unite' => ''),
            // Coordonnées
            'longitude'			=> array('cle' => array('longitude'), 'suffixe_unite' => ''),
            'latitude'			=> array('cle' => array('latitude'), 'suffixe_unite' => ''),
            // Données démographiques
            'population'		=> array('cle' => array('population', 0, 'value'), 'suffixe_unite' => ''),
       		// Informations complémentaires : aucune configuration car ce sont des données calculées
       	),
    ),
);


/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
 * unique de chargement des données météorologiques `charger_meteo()`.
 * PACKAGE SPIP\RAINETTE\WWO\API
 * ------------------------------------------------------------------------------------------------
 */

/**
 * @param string	$lieu
 * @param string	$mode
 *
 * @return string
 */
function wwo_service2cache($lieu, $mode) {
	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'wwo');
	$fichier_cache = $dir . str_replace(array(' ', ',', '+', '.'), array('', '-', '-', '-'), $lieu) . "_" . $mode . ".txt";

	return $fichier_cache;
}

/**
 * @param string	$lieu
 * @param string	$mode
 * @return string
 */
function wwo_service2url($lieu, $mode) {
	include_spip('inc/config');
	$cle = lire_config('rainette/wwo/inscription');
	$format = lire_config('rainette/wwo/format', 'xml');

	$url = _RAINETTE_WWO_URL_BASE
		.  '?key=' . $cle
		.  '&format=' . $format
		.  '&extra=localObsTime'
		.  '&q=' . str_replace(' ', '', trim($lieu)); //todo : ne faut-il pas remplacer par + ?
	if ($mode == 'infos') {
		$url .= '&includeLocation=yes&cc=no&fx=no';
	}
	else {
		$url .= ($mode == 'previsions') ? '&cc=no&fx=yes&tp=24&num_of_days=' . _RAINETTE_WWO_JOURS_PREVISIONS : '&cc=yes&fx=no';
	}

	return $url;
}

/**
 * @param string $mode
 * @return int
 */
function wwo_service2reload_time($mode) {
	static $reload = array('conditions' => 10800, 'previsions' => 14400);

	return $reload[$mode];
}

/**
 * @param string	$url
 * @return array
 */
function wwo_url2flux($url) {
	// Déterminer le format d'échange pour aiguiller vers la bonne conversion
	include_spip('inc/config');
	$format = lire_config('rainette/wwo/format', 'xml');

	include_spip('inc/phraser');
	$flux = ($format == 'xml') ? url2flux_xml($url, false) : url2flux_json($url);

	return $flux;
}


/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * ne gere pas encore le jour et la nuit de la date courante suivant l'heure!!!!
 * @param array $flux
 * @param string $lieu
 * @return array
 */
function wwo_flux2previsions($flux, $lieu) {
	// Identifier le format d'échange des données
	include_spip('inc/config');
	$format = lire_config('rainette/wwo/format', 'xml');

	// Identification des suffixes d'unite pour choisir le bon champ
	// -> wwo fournit des valeurs dans les deux systemes d'unites mais pas toutes
	include_spip('inc/config');
	$unite = lire_config('rainette/wwo/unite', 'm');
	if ($unite == 'm')
		$suffixes = explode(':', _RAINETTE_WWO_SUFFIXE_METRIQUE);
	else
		$suffixes = explode(':', _RAINETTE_WWO_SUFFIXE_STANDARD);

	// Construire le tableau standard des prévisions météorologiques propres au service
	$tableau = ($format == 'xml') ? xml2previsions_wwo($flux, $suffixes) : json2previsions_wwo($flux, $suffixes);

	// Compléter le tableau standard avec les états météorologiques calculés pour chaque jour
	if ($tableau) {
		$condition = lire_config('rainette/wwo/condition', 'wwo');
		foreach ($tableau as $_index => $_prevision) {
			if ($_prevision[0]['code_meteo']
			AND $_prevision[0]['icon_meteo']
			AND isset($_prevision[0]['desc_meteo'])) {
				// Le mode jour/nuit n'est pas supporté par ce service.
				$tableau[$_index]['periode'] = 0; // jour

				// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
				if ($condition == 'wwo') {
					// On affiche les prévisions natives fournies par le service.
					// Pour le resume, wwo ne fournit pas de traduction : on stocke donc le code meteo afin
					// de le traduire à partir des fichiers de langue SPIP.
					$tableau[$_index][0]['icone']['code'] = $_prevision[0]['code_meteo'];
					$tableau[$_index][0]['icone']['url'] = copie_locale($_prevision[0]['icon_meteo']);
					$tableau[$_index][0]['resume'] = $_prevision[0]['code_meteo'];
				}
				else {
					// On affiche les conditions traduites dans le systeme weather.com
					$meteo = meteo_wwo2weather($_prevision[0]['code_meteo'], $tableau[$_index]['periode']);
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
	$tableau[$index]['max_jours'] = _RAINETTE_WWO_JOURS_PREVISIONS;

	return $tableau;
}

/**
 * @param array 	$flux
 * @param string	$lieu
 * @return array
 */
function wwo_flux2conditions($flux, $lieu) {
	global $rainette_wwo_config;

	// Identifier le format d'échange des données
	include_spip('inc/config');
	$format = lire_config('rainette/wwo/format', 'xml');

	// Identification des suffixes d'unite pour choisir le bon champ
	// -> wwo fournit des valeurs dans les deux systemes d'unites mais pas toutes
	$systeme_unite = lire_config('rainette/wwo/unite', 'm');

	// Initialiser le tableau des données météo directement fournies par le service
	include_spip('inc/normaliser');
	$tableau = get_donnees_service($rainette_wwo_config, 'conditions', $format, $systeme_unite, $flux);

	if ($tableau) {
		// Convertir les informations exprimées en système métrique dans le systeme US si la
		// configuration le demande
		if ($systeme_unite == 's') {
			include_spip('inc/convertir');
			// Seules la température, la température ressentie et la vitesse du vent sont fournies dans
			// les deux systèmes.
			$tableau['visibilite'] = ($tableau['visibilite'])
				? kilometre2mile($tableau['visibilite']) : '';
			$tableau['pression'] = ($tableau['pression'])
				? millibar2inch($tableau['pression']) : '';
			$tableau['precipitation'] = ($tableau['precipitation'])
				? millimetre2inch($tableau['precipitation']) : '';
		}

		// Compléter le tableau standard avec les états météorologiques calculés
		if ($tableau['code_meteo']
		AND $tableau['icon_meteo']
		AND isset($tableau['desc_meteo'])) {
			// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
			// Pour ce service aucun indicateur n'est disponible
			// -> on utilise le nom de l'icone qui contient l'indication "night"
			$icone = basename($tableau['icon_meteo']);
			if (strpos($icone, '_night') === false)
				$tableau['periode'] = 0; // jour
			else
				$tableau['periode'] = 1; // nuit

			// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
			$condition = lire_config('rainette/wwo/condition', 'wwo');
			if ($condition == 'wwo') {
				// On affiche les conditions natives fournies par le service.
				// Pour le resume, wwo ne fournit pas de traduction : on stocke donc le code meteo afin
				// de le traduire à partir des fichiers de langue SPIP.
				$tableau['icone']['code'] = $tableau['code_meteo'];
				$tableau['icone']['url'] = copie_locale($tableau['icon_meteo']);
				$tableau['resume'] = $tableau['code_meteo'];
			}
			else {
				// On affiche les conditions traduites dans le systeme weather.com
				$meteo = meteo_wwo2weather($tableau['code_meteo'], $tableau['periode']);
				$tableau['icone'] = $meteo;
				$tableau['resume'] = $meteo;
			}
		}
	} else {
		// Traitement des erreurs de flux
		$tableau['erreur'] = 'chargement';
	}

	return $tableau;
}


/**
 * @param array 	$flux
 * @param string	$lieu
 * @return array
 */
function wwo_flux2infos($flux, $lieu) {
	global $rainette_wwo_config;

	// Identifier le format d'échange des données
	include_spip('inc/config');
	$format = lire_config('rainette/wwo/format', 'xml');
	$systeme_unite = lire_config('rainette/wwo/unite', 'm');

	// Initialiser le tableau normalisé des informations à partir des données du service
	include_spip('inc/normaliser');
	$tableau = get_donnees_service($rainette_wwo_config, 'infos', $format, $systeme_unite, $flux);

	if ($tableau) {
		// Ajouter les informations calculées, à savoir, le nombre de jours de prévisions
		$tableau['max_previsions'] = _RAINETTE_WWO_JOURS_PREVISIONS;
	} else {
		// Traitement des erreurs de flux
		$tableau['erreur'] = 'chargement';
	}

	return $tableau;
}

/**
 * @return array
 */
function wwo_service2credits() {

	$credits['titre'] = 'Free local weather content provider';
	$credits['logo'] = NULL;
	$credits['lien'] = 'http://www.worldweatheronline.com/';

	return $credits;
}


/**
 * -----------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont permettent le décodage des données météorologiques reçues au
 * format XML. Ce sont des sous-fonctions internes appelées uniquement par les fonctions de l'API.
 * PACKAGE SPIP\RAINETTE\WWO\XML
 * -----------------------------------------------------------------------------------------------
 */

function xml2previsions_wwo($flux, $suffixes) {
	$tableau = array();

	if (isset($flux['children']['weather'])) {
		$previsions = $flux['children']['weather'];
		$maintenant = time();

		if ($previsions) {
			foreach ($previsions as $_index => $_prevision) {
				if (isset($_prevision['children'])) {
					$_prevision = $_prevision['children'];
					// Index du jour et date du jour
					$tableau[$_index]['index'] = $_index;
					$tableau[$_index]['date'] = (isset($_prevision['date']))
						? $_prevision['date'][0]['text']
						: date('Y-m-d', $maintenant + 24*3600*$_index);

					// Date complete des lever/coucher du soleil
					$tableau[$_index]['lever_soleil'] = (isset($_prevision['astronomy'][0]['children']['sunrise'])) ? $_prevision['astronomy'][0]['children']['sunrise'][0]['text'] : '';
					$tableau[$_index]['coucher_soleil'] = (isset($_prevision['astronomy'][0]['children']['sunset'])) ? $_prevision['astronomy'][0]['children']['sunset'][0]['text'] : '';

					// Previsions du jour
					list($ut, $ur, $uv) = $suffixes;
					$tableau[$_index][0]['temperature_max'] = (isset($_prevision["maxtemp$ut"])) ? floatval($_prevision["maxtemp$ut"][0]['text']) : '';
					$tableau[$_index][0]['temperature_min'] = (isset($_prevision["mintemp$ut"])) ? floatval($_prevision["mintemp$ut"][0]['text']) : '';
					$tableau[$_index][0]['vitesse_vent'] = (isset($_prevision['hourly'][0]['children']["windspeed$uv"])) ? floatval($_prevision['hourly'][0]['children']["windspeed$uv"][0]['text']) : '';
					$tableau[$_index][0]['angle_vent'] = (isset($_prevision['hourly'][0]['children']['winddirdegree'])) ? intval($_prevision['hourly'][0]['children']['winddirdegree'][0]['text']) : '';
					$tableau[$_index][0]['direction_vent'] = (isset($_prevision['hourly'][0]['children']['winddir16point'])) ? $_prevision['hourly'][0]['children']['winddir16point'][0]['text'] : '';

					$tableau[$_index][0]['risque_precipitation'] = (isset($_prevision['hourly'][0]['children']['chanceofrain'])) ? intval($_prevision['hourly'][0]['children']['chanceofrain'][0]['text']) : '';
					include_spip('inc/convertir');
					$tableau[$_index][0]['precipitation'] = (isset($_prevision['hourly'][0]['children']['precipmm'])) ? floatval($_prevision['hourly'][0]['children']['precipmm'][0]['text']) : '';
					if (($ur == 'in') AND $tableau[$_index][0]['precipitation'])
						$tableau[$_index][0]['precipitation'] = millimetre2inch($tableau[$_index][0]['precipitation']);
					$tableau[$_index][0]['humidite'] = (isset($_prevision['hourly'][0]['children']['humidity'])) ? intval($_prevision['hourly'][0]['children']['humidity'][0]['text']) : '';

					$tableau[$_index][0]['indice_uv'] = (isset($_prevision['uvindex'])) ? intval($_prevision['uvindex'][0]['text']) : '';

					$tableau[$_index][0]['code_meteo'] = (isset($_prevision['hourly'][0]['children']['weathercode'])) ? intval($_prevision['hourly'][0]['children']['weathercode'][0]['text']) : '';
					$tableau[$_index][0]['icon_meteo'] = (isset($_prevision['hourly'][0]['children']['weathericonurl'])) ? $_prevision['hourly'][0]['children']['weathericonurl'][0]['text'] : '';
					$tableau[$_index][0]['desc_meteo'] = (isset($_prevision['hourly'][0]['children']['weatherdesc'])) ? $_prevision['hourly'][0]['children']['weatherdesc'][0]['text'] : '';

					// Previsions de la nuit si elle existe
					$tableau[$_index][1] = NULL;
				}
			}
		}
	}

	return $tableau;
}


function xml2conditions_wwo($flux, $suffixes) {
	$tableau = array();

	if (isset($flux['children']['current_condition'][0]['children'])) {
		$conditions = $flux['children']['current_condition'][0]['children'];

		// Date d'observation
		$date_maj = (isset($conditions['localobsdatetime'])) ? strtotime($conditions['localobsdatetime'][0]['text']) : '';
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		$tableau['station'] = NULL;

		// Liste des conditions meteo extraites dans le systeme demandé ou dans le système
		// metrique si celle-ci ne sont pas retournées dans le système impérial et donc
		// nécessitent une conversion
		list($ut, , $uv) = $suffixes;

		$tableau['vitesse_vent'] = (isset($conditions["windspeed$uv"])) ? floatval($conditions["windspeed$uv"][0]['text']) : '';
		$tableau['angle_vent'] = (isset($conditions['winddirdegree'])) ? intval($conditions['winddirdegree'][0]['text']) : '';
		$tableau['direction_vent'] = (isset($conditions['winddir16point'])) ? $conditions['winddir16point'][0]['text'] : '';

		$tableau['temperature_reelle'] = (isset($conditions["temp_$ut"])) ? floatval($conditions["temp_$ut"][0]['text']) : '';
		$tableau['temperature_ressentie'] = (isset($conditions["feelslike$ut"])) ? floatval($conditions["feelslike$ut"][0]['text']) : '';

		$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity'][0]['text']) : '';
		$tableau['precipitation'] = (isset($conditions['precipmm'])) ? floatval($conditions['precipmm'][0]['text']) : '';
		$tableau['point_rosee'] = NULL;

		$tableau['pression'] = (isset($conditions['pressure'])) ? floatval($conditions['pressure'][0]['text']) : '';
		$tableau['tendance_pression'] = NULL;

		$tableau['visibilite'] = (isset($conditions['visibility'])) ? floatval($conditions['visibility'][0]['text']) : '';

		$tableau['indice_uv'] = NULL;

		// Code meteo, resume et icone natifs au service
		$tableau['code_meteo'] = (isset($conditions['weathercode'])) ? intval($conditions['weathercode'][0]['text']) : '';
		$tableau['icon_meteo'] = (isset($conditions['weathericonurl'])) ? $conditions['weathericonurl'][0]['text'] : '';
		$tableau['desc_meteo'] = (isset($conditions['weatherdesc'])) ? $conditions['weatherdesc'][0]['text'] : '';
	}

	return $tableau;
}

function xml2infos_wwo($flux) {
	$tableau = array();

	if (isset($flux['children']['nearest_area'][0]['children'])) {
		$infos = $flux['children']['nearest_area'][0]['children'];

		if (isset($infos['areaname'])) {
			$tableau['ville'] = $infos['areaname'][0]['text'];
			$tableau['ville'] .= (isset($infos['country'])) ? ', ' . $infos['country'][0]['text'] : '';
		}
		$tableau['region'] = (isset($infos['region'])) ? $infos['region'][0]['text'] : '';

		$tableau['longitude'] = (isset($infos['longitude'])) ? floatval($infos['longitude'][0]['text']) : '';
		$tableau['latitude'] = (isset($infos['latitude'])) ? floatval($infos['latitude'][0]['text']) : '';

		$tableau['population'] = (isset($infos['population'])) ? intval($infos['population'][0]['text']) : '';
	}

	return $tableau;
}

/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont permettent le décodage des données météorologiques reçues au
 * format JSON. Ce sont des sous-fonctions internes appelées uniquement par les fonctions de l'API.
 * PACKAGE SPIP\RAINETTE\WWO\JSON
 * ------------------------------------------------------------------------------------------------
 */

function json2previsions_wwo($flux, $suffixes) {
	$tableau = array();

	if (isset($flux['data']['weather'])) {
		$previsions = $flux['data']['weather'];
		$maintenant = time();

		if ($previsions) {
			foreach ($previsions as $_index => $_prevision) {
				if ($_prevision) {
					// Index du jour et date du jour
					$tableau[$_index]['index'] = $_index;
					$tableau[$_index]['date'] = (isset($_prevision['date']))
						? $_prevision['date']
						: date('Y-m-d', $maintenant + 24*3600*$_index);

					// Date complete des lever/coucher du soleil
					$tableau[$_index]['lever_soleil'] = (isset($_prevision['astronomy'][0]['sunrise'])) ? $_prevision['astronomy'][0]['sunrise'] : '';
					$tableau[$_index]['coucher_soleil'] = (isset($_prevision['astronomy'][0]['sunset'])) ? $_prevision['astronomy'][0]['sunset'] : '';

					// Previsions du jour
					list($ut, $ur, $uv) = array_map('ucfirst', $suffixes);
					$tableau[$_index][0]['temperature_max'] = (isset($_prevision["maxtemp$ut"])) ? floatval($_prevision["maxtemp$ut"]) : '';
					$tableau[$_index][0]['temperature_min'] = (isset($_prevision["mintemp$ut"])) ? floatval($_prevision["mintemp$ut"]) : '';
					$tableau[$_index][0]['vitesse_vent'] = (isset($_prevision["windspeed$uv"])) ? floatval($_prevision["windspeed$uv"]) : '';
					$tableau[$_index][0]['angle_vent'] = (isset($_prevision['winddirDegree'])) ? intval($_prevision['winddirDegree']) : '';
					$tableau[$_index][0]['direction_vent'] = (isset($_prevision['winddir16Point'])) ? $_prevision['winddir16Point'] : '';

					$tableau[$_index][0]['risque_precipitation'] = NULL;
					include_spip('inc/convertir');
					$tableau[$_index][0]['precipitation'] = (isset($_prevision['precipMM'])) ? floatval($_prevision['precipMM']) : '';
					if (($ur == 'in') AND $tableau[$_index][0]['precipitation'])
						$tableau[$_index][0]['precipitation'] = millimetre2inch($tableau[$_index][0]['precipitation']);
					$tableau[$_index][0]['humidite'] = NULL;

					$tableau[$_index][0]['code_meteo'] = (isset($_prevision['weatherCode'])) ? intval($_prevision['weatherCode']) : '';
					$tableau[$_index][0]['icon_meteo'] = (isset($_prevision['weatherIconUrl'])) ? $_prevision['weatherIconUrl'][0]['value'] : '';
					$tableau[$_index][0]['desc_meteo'] = (isset($_prevision['weatherDesc'])) ? $_prevision['weatherDesc'][0]['value'] : '';

					// Previsions de la nuit si elle existe
					$tableau[$_index][1] = NULL;
				}
			}
		}
	}

	return $tableau;
}

function json2conditions_wwo($flux, $suffixes) {
	$tableau = array();

	if (isset($flux['data']['current_condition'][0])) {
		$conditions = $flux['data']['current_condition'][0];

		// Date d'observation
		$date_maj = (isset($conditions['localObsDateTime'])) ? strtotime($conditions['localObsDateTime']) : '';
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		$tableau['station'] = NULL;

		// Liste des conditions meteo extraite dans le systeme metrique
		list($ut, , $uv) = array_map('ucfirst', $suffixes);
		$tableau['vitesse_vent'] = (isset($conditions["windspeed$uv"])) ? floatval($conditions["windspeed$uv"]) : '';
		$tableau['angle_vent'] = (isset($conditions['winddirDegree'])) ? intval($conditions['winddirDegree']) : '';
		$tableau['direction_vent'] = (isset($conditions['winddir16Point'])) ? $conditions['winddir16Point'] : '';

		$tableau['temperature_reelle'] = (isset($conditions["temp_$ut"])) ? floatval($conditions["temp_$ut"]) : '';
		$tableau['temperature_ressentie'] = (isset($conditions["FeelsLike$ut"])) ? floatval($conditions["FeelsLike$ut"]) : '';

		$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity']) : '';
		$tableau['precipitation'] = (isset($conditions['precipMM'])) ? floatval($conditions['precipMM']) : '';
		$tableau['point_rosee'] = NULL;

		$tableau['pression'] = (isset($conditions['pressure'])) ? floatval($conditions['pressure']) : '';
		$tableau['tendance_pression'] = NULL;

		$tableau['visibilite'] = (isset($conditions['visibility'])) ? floatval($conditions['visibility']) : '';

		$tableau['indice_uv'] = NULL;

		// Code meteo, resume et icone natifs au service
		$tableau['code_meteo'] = (isset($conditions['weatherCode'])) ? intval($conditions['weatherCode']) : '';
		$tableau['icon_meteo'] = (isset($conditions['weatherIconUrl'])) ? $conditions['weatherIconUrl'][0]['value'] : '';
		$tableau['desc_meteo'] = (isset($conditions['weatherDesc'])) ? $conditions['weatherDesc'][0]['value'] : '';
	}

	return $tableau;
}

function json2infos_wwo($flux) {
	$tableau = array();

	if (isset($flux['data']['nearest_area'][0])) {
		$infos = $flux['data']['nearest_area'][0];

		if (isset($infos['areaName'])) {
			$tableau['ville'] = $infos['areaName'][0]['value'];
			$tableau['ville'] .= (isset($infos['country'])) ? ', ' . $infos['country'][0]['value'] : '';
		}
		$tableau['region'] = (isset($infos['region'])) ? $infos['region'][0]['value'] : '';

		$tableau['longitude'] = (isset($infos['longitude'])) ? floatval($infos['longitude']) : '';
		$tableau['latitude'] = (isset($infos['latitude'])) ? floatval($infos['latitude']) : '';

		$tableau['population'] = (isset($infos['population'])) ? intval($infos['population']) : '';
	}

	return $tableau;
}


/**
 * ---------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont des utilitaires utilisés uniquement appelées par les fonctions
 * de l'API.
 * PACKAGE SPIP\RAINETTE\WWO\OUTILS
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
function meteo_wwo2weather($meteo, $periode=0) {
	static $wwo2weather = array(
							395=> array(41,46),
							392=> array(41,46),
							389=> array(38,47),
							386=> array(37,47),
							377=> array(6,6),
							374=> array(6,6),
							371=> array(14,14),
							368=> array(13,13),
							365=> array(6,6),
							362=> array(6,6),
							359=> array(11,11),
							356=> array(11,11),
							353=> array(9,9),
							350=> array(18,18),
							338=> array(16,16),
							335=> array(16,16),
							332=> array(14,14),
							329=> array(14,14),
							326=> array(13,13),
							323=> array(13,13),
							320=> array(18,18),
							317=> array(18,18),
							314=> array(8,8),
							311=> array(8,8),
							308=> array(40,40),
							305=> array(39,45),
							302=> array(11,11),
							299=> array(39,45),
							296=> array(9,9),
							293=> array(9,9),
							284=> array(10,10),
							281=> array(9,9),
							266=> array(9,9),
							263=> array(9,9),
							260=> array(20,20),
							248=> array(20,20),
							230=> array(16,16),
							227=> array(15,15),
							200=> array(38,47),
							185=> array(10,10),
							182=> array(18,18),
							179=> array(16,16),
							176=> array(40,49),
							143=> array(20,20),
							122=> array(26,26),
							119=> array(28,27),
							116=> array(30,29),
							113=> array(32,31));

	$icone = 'na';
	if (array_key_exists($meteo,  $wwo2weather))
		$icone = strval($wwo2weather[$meteo][$periode]);
	return $icone;
}

?>