<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service Open Weather Map (owm).
 * Ce service fournit des données au format XML ou JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\OWM
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_OWM_URL_BASE_REQUETE')) {
	define('_RAINETTE_OWM_URL_BASE_REQUETE', 'http://api.openweathermap.org/data/2.5/');
}
if (!defined('_RAINETTE_OWM_URL_BASE_ICONE')) {
	define('_RAINETTE_OWM_URL_BASE_ICONE', 'http://openweathermap.org/img/w');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_owm_config']['service'] = array(
	'alias'   => 'owm',
	'defauts' => array(
		'inscription' => '',
		'unite'       => 'm',
		'condition'   => 'owm',
		'theme'       => '',
	),
	'credits' => array(
		'titre'       => null,
		'logo'        => null,
		'lien'        => 'http://openweathermap.org/',
	),
	'langues' => array(
		'disponibles' => array(
			'bg' => 'bg',
			'ca' => 'ca',
			'de' => 'de',
			'en' => 'en',
			'es' => 'es',
			'fi' => 'fi',
			'fr' => 'fr',
			'hr' => 'hr',
			'it' => 'it',
			'nl' => 'nl',
			'pl' => 'pl',
			'pt' => 'pt',
			'ro' => 'ro',
			'ru' => 'ru',
			'sv' => 'sv',
			'tr' => 'tr',
			'uk' => 'uk',
			'zh' => 'zh',
			'zh_tw' => 'zh_tw',
		),
		'defaut'      => 'en'
	)
);

// Configuration des données fournies par le service wunderground pour le mode 'infos'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_owm_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array(),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('name')),
		'pays'      => array('cle' => array()),
		'pays_iso2' => array('cle' => array('sys', 'country')),
		'region'    => array('cle' => array()),
		// Coordonnées
		'longitude' => array('cle' => array('coord', 'lon')),
		'latitude'  => array('cle' => array('coord', 'lat')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service owm pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
// -- On utilise le mode XML et non JSON car la date de dernière mise à jour n'est pas disponible en JSON
$GLOBALS['rainette_owm_config']['conditions'] = array(
	'periode_maj' => 7200,
	'format_flux' => 'xml',
	'cle_base'    => array('children'),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('lastupdate', 0, 'attributes', 'value')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('temperature', 0, 'attributes', 'value')),
		'temperature_ressentie' => array('cle' => array()),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('wind', 0, 'children', 'speed', 0, 'attributes', 'value')),
		'angle_vent'            => array('cle' => array('wind', 0, 'children', 'direction', 0, 'attributes', 'value')),
		'direction_vent'        => array('cle' => array('wind', 0, 'children', 'direction', 0, 'attributes', 'code')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array()),
		'humidite'              => array('cle' => array('humidity', 0, 'attributes', 'value')),
		'point_rosee'           => array('cle' => array()),
		'pression'              => array('cle' => array('pressure', 0, 'attributes', 'value')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('visibility', 0, 'attributes', 'value')),
		'indice_uv'             => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('weather', 0, 'attributes', 'number')),
		'icon_meteo'            => array('cle' => array('weather', 0, 'attributes', 'icon')),
		'desc_meteo'            => array('cle' => array('weather', 0, 'attributes', 'value')),
		'trad_meteo'            => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service owm pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
// -- On utilise le mode XML et non JSON car la date de dernière mise à jour et la précipitation ne sont
//    pas disponibles en JSON
$GLOBALS['rainette_owm_config']['previsions'] = array(
	'periodicites'       => array(
		24                     => array('max_jours' => 16),
//		3                      => array('max_jours' => 5)
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 7200,
	'format_flux'        => 'xml',
	'cle_base'           => array('children', 'forecast', 0, 'children', 'time'),
	'cle_heure'          => array(),
	'structure_heure'    => false,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('attributes', 'day')),
		'heure'                => array('cle' => array()),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array()),
		'coucher_soleil'       => array('cle' => array()),
		// Températures
		'temperature'          => array('cle' => array()),
		'temperature_max'      => array('cle' => array('children', 'temperature', 0, 'attributes', 'max')),
		'temperature_min'      => array('cle' => array('children', 'temperature', 0, 'attributes', 'min')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('children', 'windspeed', 0, 'attributes', 'mps')),
		'angle_vent'           => array('cle' => array('children', 'winddirection', 0, 'attributes', 'deg')),
		'direction_vent'       => array('cle' => array('children', 'winddirection', 0, 'attributes', 'code')),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array()),
		'precipitation'        => array('cle' => array('children', 'precipitation', 0, 'attributes', 'value')),
		'humidite'             => array('cle' => array('children', 'humidity', 0, 'attributes', 'value')),
		'point_rosee'          => array('cle' => array()),
		'pression'             => array('cle' => array('children', 'pressure', 0, 'attributes', 'value')),
		'visibilite'           => array('cle' => array()),
		'indice_uv'            => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('children', 'symbol', 0, 'attributes', 'number')),
		'icon_meteo'           => array('cle' => array('children', 'symbol', 0, 'attributes', 'var')),
		'desc_meteo'           => array('cle' => array('children', 'symbol', 0, 'attributes', 'name')),
		'trad_meteo'           => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
 * unique de chargement des données météorologiques `charger_meteo()`.
 * PACKAGE SPIP\RAINETTE\OWM\API
 * ------------------------------------------------------------------------------------------------
 */

/**
 * @param string $mode
 *
 * @return string
 */
function owm_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dit
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_owm_config'][$mode], $GLOBALS['rainette_owm_config']['service']);

	return $config;
}


/**
 * @param $lieu
 * @param $mode
 * @param $periodicite
 * @param $configuration
 *
 * @return string
 */
function owm_service2url($lieu, $mode, $periodicite, $configuration) {

	// Determination de la demande
	$demande = ($mode == 'previsions') ? 'forecast' : 'weather';
	if ($periodicite == 24) {
		$demande .= '/daily';
	}

	// Identification de la langue du resume.
	// Le choix de la langue n'a d'interet que si on utilise le resume natif du service. Si ce n'est pas le cas
	// on ne la precise pas et on laisse l'API renvoyer la langue par defaut
	include_spip('inc/rainette_normaliser');
	$code_langue = trouver_langue_service($configuration);

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays et le format latitude,longitude
	list($lieu_normalise, $format_lieu) = normaliser_lieu($lieu);
	if ($format_lieu == 'latitude_longitude') {
		list($latitude, $longitude) = explode(',', $lieu_normalise);
		$query = "lat=${latitude}&lon=${longitude}";
	} else {
		// Format ville,pays
		$query = "q=${lieu_normalise}";
	}

	$url = _RAINETTE_OWM_URL_BASE_REQUETE
		   . $demande . '?'
		   . $query
		   . '&mode=' . $configuration['format_flux']
		   . '&units=' . ($configuration['unite'] == 'm' ? 'metric' : 'imperial')
		   . ((($mode == 'previsions') and ($periodicite == 24))
			? '&cnt=' . $configuration['periodicites'][$periodicite]['max_jours']
			: '')
		   . '&lang=' . $code_langue
		   . ($configuration['inscription'] ? '&APPID=' . $configuration['inscription'] : '');

	return $url;
}


/**
 * @param array $tableau
 * @param       $configuration
 *
 * @return array
 */
function owm_complement2infos($tableau, $configuration) {
	// Aucune donnée à rajouter en complément au tableau initialisé
	// TODO : remplir le nom du pays à partir du code ISO 3166-1 alpha 2.
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
 *
 * @return array
 *        Tableau standardisé des conditions météorologiques complété par les données spécifiques
 *        du service.
 */
function owm_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Calcul de la température ressentie et de la direction du vent (16 points), celles-ci
		// n'étant pas fournie nativement par owm
		include_spip('inc/rainette_convertir');
		$tableau['temperature_ressentie'] = temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']);
		$tableau['direction_vent'] = angle2direction($tableau['angle_vent']);

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_owm($tableau, $configuration);
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
 *        Index où trouver et ranger les données. Cet index n'est pas utilisé pour les conditions
 *
 * @return array
 *        Tableau standardisé des conditions météorologiques complété par les données spécifiques
 *        du service.
 */
function owm_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_owm($tableau, $configuration);
	}

	return $tableau;
}


/**
 * ---------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont des utilitaires utilisés uniquement appelées par les fonctions
 * de l'API.
 * PACKAGE SPIP\RAINETTE\OWM\OUTILS
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
function etat2resume_owm(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service le nom du fichier icone finit par "d" pour le jour et
		// par "n" pour la nuit.
		$icone = $tableau['icon_meteo'];
		if (strpos($icone, 'n') === false) {
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
			$url = _RAINETTE_OWM_URL_BASE_ICONE . '/' . $tableau['icon_meteo'] . '.png';
			$tableau['icone']['url'] = copie_locale($url);
			$tableau['resume'] = ucfirst($tableau['desc_meteo']);
		} else {
			// On affiche les conditions traduites dans le systeme weather.com
			// Pour le resume on stocke le code et non la traduction pour eviter de generer
			// un cache par langue comme pour le mode natif. La traduction est faite via les fichiers de langue
			$meteo = meteo_owm2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}
}


// TODO : mettre au point le transcodage omw vers weather
function meteo_owm2weather($meteo, $periode = 0) {
	static $owm2weather = array(
		'chanceflurries'  => array(41, 46),
		'chancerain'      => array(39, 45),
		'chancesleet'     => array(39, 45),
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
	if (array_key_exists($meteo, $owm2weather)) {
		$icone = strval($owm2weather[$meteo][$periode]);
	}

	return $icone;
}
