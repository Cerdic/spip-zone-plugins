<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service Wunderground.
 * Ce service fournit des données au format XML ou JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\WUNDERGROUND
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_WUNDERGROUND_URL_BASE_REQUETE')) {
	/**
	 * URL de base (endpoint) des requêtes au service Wunderground
	 */
	define('_RAINETTE_WUNDERGROUND_URL_BASE_REQUETE', 'http://api.wunderground.com/api');
}
if (!defined('_RAINETTE_WUNDERGROUND_URL_BASE_ICONE')) {
	/**
	 * URL de base des icônes fournis par le service Wunderground
	 */
	define('_RAINETTE_WUNDERGROUND_URL_BASE_ICONE', 'http://icons.wxug.com/i/c');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_wunderground_config']['service'] = array(
	'alias'   => 'wunderground',
	'defauts' => array(
		'inscription' => '',
		'unite'       => 'm',
		'condition'   => 'wundergound',
		'theme'       => 'a',
	),
	'credits' => array(
		'titre'       => null,
		'logo'        => 'wunderground-126.png',
		'lien'        => 'http://www.wunderground.com/',
	),
	'langues' => array(
		'disponibles' => array(
			'AF' => 'af',
			'AR' => 'ar',
			'AZ' => 'az',
			'BY' => 'be',
			'BU' => 'bg',
			'CA' => 'ca',
			'HT' => 'cpf_hat',
			'CZ' => 'cs',
			'CY' => 'cy',
			'DK' => 'da',
			'DL' => 'de',
			'GR' => 'el',
			'EN' => 'en',
			'EO' => 'eo',
			'SP' => 'es',
			'ET' => 'et',
			'EU' => 'eu',
			'FA' => 'fa',
			'FI' => 'fi',
			'FR' => 'fr',
			'IR' => 'ga',
			'GZ' => 'gl',
			'GU' => 'gu',
			'IL' => 'he',
			'HI' => 'hi',
			'CR' => 'hr',
			'HU' => 'hu',
			'HY' => 'hy',
			'ID' => 'id',
			'IS' => 'is',
			'IT' => 'it',
			'JP' => 'ja',
			'JW' => 'jv',
			'KA' => 'ka',
			'KM' => 'km',
			'KR' => 'ko',
			'KU' => 'ku',
			'LA' => 'la',
			'LT' => 'lt',
			'LV' => 'lv',
			'GM' => 'man',
			'MI' => 'mi',
			'MK' => 'mk',
			'MN' => 'mn',
			'MR' => 'mr',
			'MT' => 'mt',
			'MY' => 'my',
			'NL' => 'nl',
			'NO' => 'no',
			'OC' => 'oc',
			'PA' => 'pa',
			'PL' => 'pl',
			'PS' => 'ps',
			'BR' => 'pt',
			'RO' => 'ro',
			'RU' => 'ru',
			'SK' => 'sk',
			'SL' => 'sl',
			'AL' => 'sq',
			'SR' => 'sr',
			'SW' => 'sv',
			'SI' => 'sw',
			'TH' => 'th',
			'TK' => 'tk',
			'TL' => 'tl',
			'TR' => 'tr',
			'TT' => 'tt',
			'UA' => 'uk',
			'UZ' => 'uz',
			'VU' => 'vi',
			'SN' => 'wo',
			'YI' => 'yi',
			'CN' => 'zh',
			'TW' => 'zh_tw',
		),
		'defaut'      => 'EN'
	)
);

// Configuration des données fournies par le service wunderground pour le mode 'infos'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wunderground_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array('location'),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('city')),
		'pays'      => array('cle' => array('country_name')),
		'pays_iso2' => array('cle' => array('country_iso3166')),
		'region'    => array('cle' => array('state')),
		// Coordonnées
		'longitude' => array('cle' => array('lon')),
		'latitude'  => array('cle' => array('lat')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service wwo pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wunderground_config']['conditions'] = array(
	'periode_maj' => 1800,
	'format_flux' => 'json',
	'cle_base'    => array('current_observation'),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('observation_time_rfc822')),
		'station'               => array('cle' => array('observation_location', 'full')),
		// Températures
		'temperature_reelle'    => array('cle' => array('temp_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'c', 's' => 'f')),
		'temperature_ressentie' => array('cle' => array('feelslike_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'c', 's' => 'f')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('wind_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'kph', 's' => 'mph')),
		'angle_vent'            => array('cle' => array('wind_degrees')),
		'direction_vent'        => array('cle' => array()),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array('precip_today_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'metric', 's' => 'in')),
		'humidite'              => array('cle' => array('relative_humidity')),
		'point_rosee'           => array('cle' => array('dewpoint_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'c', 's' => 'f')),
		'pression'              => array('cle' => array('pressure_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'mb', 's' => 'in')),
		'tendance_pression'     => array('cle' => array('pressure_trend')),
		'visibilite'            => array('cle' => array('visibility_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'km', 's' => 'mi')),
		'indice_uv'             => array('cle' => array('UV')),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('icon')),
		'icon_meteo'            => array('cle' => array('icon_url')),
		'desc_meteo'            => array('cle' => array('weather')),
		'trad_meteo'            => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service wwo pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wunderground_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 10),
		//		1  => array('max_jours' => 10)
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 1800,
	'format_flux'        => 'json',
	'cle_base'           => array('forecast', 'simpleforecast', 'forecastday'),
	'cle_heure'          => array(),
	'structure_heure'    => false,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('date', 'epoch')),
		'heure'                => array('cle' => array()),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array()),
		'coucher_soleil'       => array('cle' => array()),
		// Températures
		'temperature'          => array('cle' => array()),
		'temperature_max'      => array('cle' => array('high', ''), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'celsius', 's' => 'fahrenheit')),
		'temperature_min'      => array('cle' => array('low', ''), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'celsius', 's' => 'fahrenheit')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('avewind', ''), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'kph', 's' => 'mph')),
		'angle_vent'           => array('cle' => array('avewind', 'degrees')),
		'direction_vent'       => array('cle' => array()),
		// Données atmosphériques
		'risque_precipitation' => array('cle' => array()),
		'precipitation'        => array('cle' => array('qpf_allday', ''), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'mm', 's' => 'in')),
		'humidite'             => array('cle' => array('avehumidity')),
		'point_rosee'          => array('cle' => array()),
		'pression'             => array('cle' => array()),
		'visibilite'           => array('cle' => array()),
		'indice_uv'            => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('icon')),
		'icon_meteo'           => array('cle' => array('icon_url')),
		'desc_meteo'           => array('cle' => array('conditions')),
		'trad_meteo'           => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);


/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
 * unique de chargement des données météorologiques `charger_meteo()`.
 * PACKAGE SPIP\RAINETTE\WUNDERGROUND\API
 * ------------------------------------------------------------------------------------------------
 */

/**
 * @param string $mode
 *
 * @return string
 */
function wunderground_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dit
	// composée des valeurs par défaut de la configuration utilisateur et e paramètres généraux.
	$config = array_merge($GLOBALS['rainette_wunderground_config'][$mode], $GLOBALS['rainette_wunderground_config']['service']);

	return $config;
}


/**
 * Contruit l'url de la requête en fonction du lieu, du mode et de la périodicité demandés.
 *
 * @api
 *
 * @param string $lieu
 *        Lieu pour lequel on requiert le nom du cache.
 * @param string $mode
 *        Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 * @param int    $periodicite
 *        La périodicité horaire des prévisions :
 *            - `24`, ou `1`, pour le mode `previsions`
 *            - `0`, pour les modes `conditions` et `infos`
 * @param array  $configuration
 *        Configuration complète du service, statique et utilisateur.
 *
 * @return string
 *        Chemin complet du fichier cache.
 */
function wunderground_service2url($lieu, $mode, $periodicite, $configuration) {

	// Determination de la demande
	$demande = '';
	switch ($mode) {
		case 'infos':
			$demande = 'geolookup';
			break;
		case 'conditions':
			$demande = 'conditions';
			break;
		case 'previsions':
			$demande = ($periodicite == 24) ? 'forecast10day/astronomy' : 'hourly10day/astronomy';
			break;
	}

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays, le format latitude,longitude, le format adresse IP
	// et le format weather ID (comme FRXX0076 pour Paris).
	include_spip('inc/rainette_normaliser');
	list($lieu_normalise, $format_lieu) = normaliser_lieu($lieu);
	if ($format_lieu == 'weather_id') {
		$query = "locid:${lieu_normalise}";
	} elseif ($format_lieu == 'adresse_ip') {
		$query = "autoip.json?geo_ip=${lieu_normalise}";
	} elseif ($format_lieu == 'latitude_longitude') {
		$query = $lieu_normalise;
	} else { // Format ville,pays
		$query = $lieu_normalise;
		$elements = explode(',', $lieu_normalise);
		if (count($elements) == 2) {
			// Le pays est précisé, il faut alors le positionner avant la ville et le séparer par un slash.
			$query = $elements[1] . '/' . $elements[0];
		}
	}

	// Identification de la langue du resume.
	// Le choix de la langue n'a d'interet que si on utilise le resume natif du service. Si ce n'est pas
	// le cas on demande à l'API de renvoyer la langue par defaut
	$code_langue = trouver_langue_service($configuration);

	$url = _RAINETTE_WUNDERGROUND_URL_BASE_REQUETE
		   . '/' . $configuration['inscription']
		   . '/' . $demande
		   . '/lang:' . $code_langue
		   . '/q'
		   . '/' . $query . '.' . $configuration['format_flux'];

	return $url;
}


/**
 * Complète par des données spécifiques au service le tableau des conditions issu
 * uniquement de la lecture du flux.
 *
 * @api
 *
 * @param array $tableau
 *        Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *        par le service.
 * @param array $configuration
 *        Configuration complète du service, statique et utilisateur.
 *
 * @return array
 *        Tableau standardisé des conditions météorologiques complété par les données spécifiques
 *        du service.
 */
function wunderground_complement2conditions($tableau, $configuration) {
	static $tendances = array('0' => 'steady', '+' => 'rising', '-' => 'falling');

	if ($tableau) {
		// Traiter le cas où l'indice uv n'est pas fourni: wunderground renvoie une valeur négative.
		// On écrase cette valeur par la chaine vide qui indique que la donnée n'est pas disponible.
		if (is_int($tableau['indice_uv']) and $tableau['indice_uv'] < 0) {
			$tableau['indice_uv'] = '';
		}

		// Convertir la valeur de tendance dans le standard du plugin
		// La documentation indique que les directions uniques sont fournies sous forme de texte comme North
		// alors que les autres sont des acronymes. En outre, la valeur semble être traduite
		// --> Le mieux est donc de convertir à partir de l'angle
		include_spip('inc/rainette_convertir');
		$tableau['direction_vent'] = angle2direction($tableau['angle_vent']);
		// Correspondance des tendances de pression dans le système standard
		$tableau['tendance_pression'] = $tendances[$tableau['tendance_pression']];

		// Parfois le nom de la station se termine par une virgule et un espace : on supprime ces deux caractères.
		$tableau['station'] = rtrim($tableau['station'], ' ,');

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_wunderground($tableau, $configuration);
	}

	return $tableau;
}


/**
 * Complète par des données spécifiques au service le tableau des conditions issu
 * uniquement de la lecture du flux.
 *
 * @api
 *
 * @param array $tableau
 *        Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *        par le service.
 * @param array $configuration
 *        Configuration complète du service, statique et utilisateur.
 * @param int   $index_periode
 *        Index où trouver et ranger les données.
 *
 * @return array
 *        Tableau standardisé des conditions météorologiques complété par les données spécifiques
 *        du service.
 */
function wunderground_complement2previsions($tableau, $configuration, $index_periode) {

	if ($tableau and ($index_periode > -1)) {
		// Déterminer la direction du vent dans le standard du plugin.
		// La documentation indique que les directions uniques sont fournies sous forme de texte comme North
		// alors que les autres sont des acronymes. En outre, la valeur semble être traduite
		// --> Le mieux est donc de convertir à partir de l'angle
		include_spip('inc/rainette_convertir');
		$tableau['direction_vent'] = angle2direction($tableau['angle_vent']);

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_wunderground($tableau, $configuration);
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
 * Calcule les états en fonction des états météorologiques natifs fournis par le service.
 *
 * @internal
 *
 * @param array $tableau
 *        Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *        par le service. Le tableau est mis à jour et renvoyé à l'appelant.
 * @param array $configuration
 *        Configuration complète du service, statique et utilisateur.
 *
 * @return void
 */
function etat2resume_wunderground(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service (cas actuel) le nom du fichier icone commence par "nt_" pour la nuit.
		$icone = basename($tableau['icon_meteo']);
		if (strpos($icone, 'nt_') === false) {
			// C'est le jour
			$tableau['periode'] = 0;
		} else {
			// C'est la nuit
			$tableau['periode'] = 1;
		}

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		if ($configuration['condition'] == $configuration['alias']) {
			// On affiche les conditions natives fournies par le service.
			// Celles-ci etant deja traduites dans la bonne langue on stocke le texte exact retourne par l'API
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$url = _RAINETTE_WUNDERGROUND_URL_BASE_ICONE . '/'
				   . $configuration['theme'] . '/'
				   . basename($tableau['code_meteo']);
			$tableau['icone']['url'] = copie_locale($url);
			$tableau['resume'] = ucfirst($tableau['desc_meteo']);
		} else {
			// On affiche les conditions traduites dans le systeme weather.com
			// Pour le resume on stocke le code et non la traduction pour eviter de generer
			// un cache par langue comme pour le mode natif. La traduction est faite via les fichiers de langue
			$meteo = meteo_wunderground2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}
}


/**
 * @internal
 *
 * @link http://plugins.trac.wordpress.org/browser/weather-and-weather-forecast-widget/trunk/gg_funx_.php
 * Transcodage issu du plugin Wordpress weather forecast.
 *
 * @param string $meteo
 * @param int    $periode
 *
 * @return string
 */
function meteo_wunderground2weather($meteo, $periode = 0) {
	static $wunderground2weather = array(
		'chanceflurries'  => array(41, 46),
		'chancerain'      => array(39, 45),
		'chancesleet'     => array(39, 45),
		//		'chancesleet'     => array(41, 46),
		'chancesnow'      => array(41, 46),
		'chancetstorms'   => array(38, 47),
		'clear'           => array(32, 31),
		'cloudy'          => array(26, 26),
		'flurries'        => array(15, 15),
		'fog'             => array(20, 20),
		'hazy'            => array(21, 21),
		'mostlycloudy'    => array(28, 27),
		'mostlysunny'     => array(34, 33),
		'partlycloudy'    => array(30, 29),
		'partlysunny'     => array(28, 27),
		'sleet'           => array(5, 5),
		'rain'            => array(11, 11),
		'snow'            => array(16, 16),
		'sunny'           => array(32, 31),
		'tstorms'         => array(4, 4),
		'thunderstorms'   => array(4, 4),
		'unknown'         => array(4, 4),
		'scatteredclouds' => array(30, 29),
		'overcast'        => array(26, 26)
	);

	$icone = 'na';
	$meteo = strtolower($meteo);
	if (array_key_exists($meteo, $wunderground2weather)) {
		$icone = strval($wunderground2weather[$meteo][$periode]);
	}

	return $icone;
}
