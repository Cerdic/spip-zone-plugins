<?php
/**
 * Ce fichier contient la configuration et l'ensemble des fonctions implémentant le service AccuWeather (accuweather).
 * Ce service est capable de fournir des données au format JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\ACCUWEATHER
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_ACCUWEATHER_URL_BASE')) {
	/**
	 * URL de base (endpoint) des requêtes au service World Weather Online.
	 */
	define('_RAINETTE_ACCUWEATHER_URL_BASE', 'http://dataservice.accuweather.com/');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_accuweather_config']['service'] = array(
	'alias'   => 'accuweather',
	'nom'     => 'AccuWeather',
	'actif'   => true,
	'credits' => array(
		'titre'       => 'AccuWeather',
		'logo'        => 'accuweather.png',
		'lien'        => 'https://www.accuweather.com/',
	),
	'termes'         => array(
		'titre' => 'Terms of Use',
		'lien' => 'https://developer.accuweather.com/legal'
	),
	'enregistrement' => array(
		'titre' => 'User Account',
		'lien' => 'https://developer.accuweather.com/user/register',
		'taille_cle' => 32
	),
	'offres'         => array(
		'titre' => 'Packages',
		'lien' => 'https://developer.accuweather.com/packages',
		'limites' => array(
			'day' => 50
		),
	),
	'langues' => array(
		'disponibles' => array(
			'ar' => 'ar',
			'az' => 'az',
			'bn' => 'bn',
			'bs' => 'bs',
			'bg' => 'bg',
			'ca' => 'ca',
			'zh' => 'zh',
			'hr' => 'hr',
			'cs' => 'cs',
			'da' => 'da',
			'nl' => 'nl',
			'en' => 'en',
			'et' => 'et',
			'fa' => 'fa',
			'fil' => '',
			'fi' => 'fi',
			'fr' => 'fr',
			'de' => 'de',
			'el' => 'el',
			'gu' => 'gu',
			'he' => 'he',
			'hi' => 'hi',
			'hu' => 'hu',
			'is' => 'is',
			'id' => 'id',
			'it' => 'it',
			'ja' => 'ja',
			'kn' => 'kn',
			'kk' => 'kk',
			'ko' => 'ko',
			'lv' => 'lv',
			'lt' => 'lt',
			'mk' => 'mk',
			'ms' => 'ms',
			'mr' => 'mr',
			'nb' => 'nb',
			'pl' => 'pl',
			'pt' => 'pt',
			'pa' => 'pa',
			'ro' => 'ro',
			'ru' => 'ru',
			'sr' => 'sr',
			'sk' => 'sk',
			'sl' => 'sl',
			'es' => 'es',
			'sw' => 'sw',
			'sv' => 'sv',
			'tl' => 'tl',
			'ta' => 'ta',
			'te' => 'te',
			'th' => 'th',
			'tr' => 'tr',
			'uk' => 'uk',
			'ur' => 'ur',
			'uz' => 'uz',
			'vi' => 'vi',
			'zh-tw' => 'zh_tw',
		),
		'defaut'      => 'en'
	),
	'defauts' => array(
		'inscription'   => '',
		'unite'         => 'm',
		'condition'     => 'accuweather',
		'theme'         => '',
		'theme_local'   => 'original',
		'theme_weather' => 'sticker',
	),
	'transcodage_weather' => array(
		1  => array(32, 32),
		2  => array(34, 34),
		3  => array(30, 30),
		4  => array(30, 30),
		5  => array(21, 21),
		6  => array(28, 28),
		7  => array(26, 26),
		8  => array(28, 27),
		11 => array(20, 20),
		12 => array(11, 11),
		13 => array(39, 39),
		14 => array(39, 39),
		15 => array(4, 4),
		16 => array(38, 38),
		17 => array(37, 37),
		18 => array(12, 12),
		19 => array(13, 13),
		20 => array(41, 41),
		21 => array(41, 41),
		22 => array(16, 16),
		23 => array(41, 41),
		24 => array(17, 17),
		25 => array(18, 18),
		26 => array(10, 10),
		29 => array(5, 5),
		30 => array(36, 36),
		31 => array(25, 25),
		32 => array(24, 24),
		33 => array(31, 31),
		34 => array(33, 33),
		35 => array(29, 29),
		36 => array(29, 29),
		37 => array(21, 21),
		38 => array(27, 27),
		39 => array(45, 45),
		40 => array(45, 45),
		41 => array(47, 47),
		42 => array(47, 47),
		43 => array(46, 46),
		44 => array(46, 46)
	)
);

// Configuration des données fournies par le service AccuWeather pour le mode 'infos' en format JSON.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_accuweather_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array(),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('LocalizedName')),
		'pays'      => array('cle' => array('Country', 'LocalizedName')),
		'pays_iso2' => array('cle' => array('Country', 'ID')),
		'region'    => array('cle' => array('Region', 'LocalizedName')),
		// Coordonnées
		'longitude' => array('cle' => array('GeoPosition', 'longitude')),
		'latitude'  => array('cle' => array('GeoPosition', 'Longitude')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service AccuWeather pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_accuweather_config']['conditions'] = array(
	'periode_maj' => 7200,
	'format_flux' => 'json',
	'cle_base'    => array(0),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('LocalObservationDateTime')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('Temperature', '', 'Value'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'Metric', 's' => 'Imperial')),
		'temperature_ressentie' => array('cle' => array('ApparentTemperature', '', 'Value'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'Metric', 's' => 'Imperial')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('Wind', 'Speed', '', 'Value'), 'suffixe_unite' => array('id_cle' => 2, 'm' => 'Metric', 's' => 'Imperial')),
		'angle_vent'            => array('cle' => array('Wind', 'Direction', 'Degrees')),
		'direction_vent'        => array('cle' => array('Wind', 'Direction', 'English')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array('Precip1hr', '', 'Value'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'Metric', 's' => 'Imperial')),
		'humidite'              => array('cle' => array('RelativeHumidity')),
		'point_rosee'           => array('cle' => array('DewPoint', '', 'Value'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'Metric', 's' => 'Imperial')),
		'pression'              => array('cle' => array('Pressure', '', 'Value'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'Metric', 's' => 'Imperial')),
		'tendance_pression'     => array('cle' => array('PressureTendency', 'Code')),
		'visibilite'            => array('cle' => array('Visibility', '', 'Value'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'Metric', 's' => 'Imperial')),
		'nebulosite'            => array('cle' => array('CloudCover')),
		'indice_uv'             => array('cle' => array('UVIndex')),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('WeatherIcon')),
		'icon_meteo'            => array('cle' => array()),
		'desc_meteo'            => array('cle' => array('WeatherText')),
		'trad_meteo'            => array('cle' => array()),
		'jour_meteo'            => array('cle' => array('IsDayTime')),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service AccuWeather pour le mode 'conditions'.
// -- L'API Premium fournit 15 jours de prévisions.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_accuweather_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 5),
		12 => array('max_jours' => 5),
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 14400,
	'format_flux'        => 'json',
	'cle_base'           => array('data', 'weather'),
	'cle_heure'          => array('hourly'),
	'structure_heure'    => true,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('date')),
		'heure'                => array('cle' => array('time')),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array('astronomy', 0, 'sunrise')),
		'coucher_soleil'       => array('cle' => array('astronomy', 0, 'sunset')),
		// Températures
		'temperature'          => array('cle' => array('temp'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		'temperature_max'      => array('cle' => array('maxtemp'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		'temperature_min'      => array('cle' => array('mintemp'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('windspeed'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'Kmph', 's' => 'Miles')),
		'angle_vent'           => array('cle' => array('winddirDegree')),
		'direction_vent'       => array('cle' => array('winddir16Point')),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array('chanceofrain')),
		'precipitation'        => array('cle' => array('precipMM')),
		'humidite'             => array('cle' => array('humidity')),
		'point_rosee'          => array('cle' => array('DewPoint'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		'pression'             => array('cle' => array('pressure')),
		'visibilite'           => array('cle' => array('visibility')),
		'nebulosite'           => array('cle' => array('cloudcover')),
		'indice_uv'            => array('cle' => array('uvIndex')),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('weatherCode')),
		'icon_meteo'           => array('cle' => array('weatherIconUrl', 0, 'value')),
		'desc_meteo'           => array('cle' => array('weatherDesc', 0, 'value')),
		'trad_meteo'           => array('cle' => array('lang_', 0, 'value'), 'suffixe_langue' => array('id_cle' => 0)),
		'jour_meteo'            => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service AccuWeather en cas d'erreur.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_accuweather_config']['erreurs'] = array(
	'cle_base'    => array(),
	'donnees'     => array(
		// Erreur
		'code'     => array('cle' => array('Code')),
		'message'  => array('cle' => array('Message')),
	),
);


// ------------------------------------------------------------------------------------------------
// Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
// unique de chargement des données météorologiques `meteo_charger()`.
// ------------------------------------------------------------------------------------------------

/**
 * Fournit la configuration statique du service pour le type de données requis.
 *
 * @api
 *
 * @param string $mode
 *        Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 *        La périodicité n'est pas nécessaire car la configuration est indifférente à ce paramètre.
 *
 * @return array
 *        Le tableau des données de configuration communes au service et propres au type de données demandé.
 */
function accuweather_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dite
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_accuweather_config'][$mode], $GLOBALS['rainette_accuweather_config']['service']);

	return $config;
}


/**
 * Construit l'url de la requête correspondant au lieu, au type de données et à la configuration utilisateur
 * du service (par exemple, le code d'inscription, le format des résultats...).
 *
 * @api
 * @uses langue_determiner()
 * @uses lieu_normaliser()
 *
 * @param string $lieu
 *        Lieu pour lequel on acquiert les données météorologiques.
 * @param string $mode
 *        Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 * @param int    $periodicite
 *        La périodicité horaire des prévisions :
 *        - `24`, `12`, `6`, `3` ou `1`, pour le mode `previsions`
 *        - `0`, pour les modes `conditions` et `infos`
 * @param array  $configuration
 *        Configuration complète du service, statique et utilisateur.
 *
 * @return string
 *        URL complète de la requête.
 */
function accuweather_service2url($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	include_spip('inc/rainette_normaliser');
	$code_langue = langue_determiner($configuration);

	// On normalise le lieu et on récupère son format.
	// Le service accepte uniquement son propre keyLocation id sous forme d'un entier.
	$lieu_normalise = lieu_normaliser($lieu);

	// On construit le mode
	if ($mode == 'infos') {
		$mode = 'location';
	} elseif ($mode == 'conditions') {
		$mode = 'currentconditions';
	} else {
		$mode = 'location';
	}

	$url = _RAINETTE_ACCUWEATHER_URL_BASE
		. $mode
		. '/v1/'
		. $lieu_normalise
		. ".{$configuration['format_flux']}"
		. '?apikey=' . $configuration['inscription']
		. '&language=' . $code_langue
		. '&details=true';

	return $url;
}


/**
 * @param array $erreur
 *
 * @return bool
 */
function accuweather_erreur_verifier($erreur) {

	// Initialisation
	$est_erreur = false;

	// Une erreur est uniquement décrite par un message.
	if (!empty($erreur['code']) or !empty($erreur['message'])) {
		$est_erreur = true;
	}

	return $est_erreur;
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
function accuweather_complement2conditions($tableau, $configuration) {

	static $tendances = array('S' => 'steady', 'R' => 'rising', 'F' => 'falling', '' => '');

	if ($tableau) {
		// Correspondance des tendances de pression dans le système standard
		$tableau['tendance_pression'] = $tendances[$tableau['tendance_pression']];

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_accuweather($tableau, $configuration);
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
function accuweather_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {
		// Convertir les informations exprimées en système métrique dans le systeme US si la
		// configuration le demande
		if ($configuration['unite'] == 's') {
			metrique2imperial_accuweather($tableau);
		}

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_accuweather($tableau, $configuration);
	}

	return $tableau;
}


/**
 * ---------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont des utilitaires uniquement appelées par les fonctions de l'API
 * ---------------------------------------------------------------------------------------------
 */

/**
 * @param array $tableau
 *
 * @return void
 */
function metrique2imperial_accuweather(&$tableau) {
	include_spip('inc/rainette_convertir');

	// Seules la température, la température ressentie et la vitesse du vent sont fournies dans
	// les deux systèmes.
	// Etant donnée que les tableaux sont normalisés, ils contiennent toujours les index de chaque
	// donnée météo, il est donc inutile de tester leur existence.
	$tableau['visibilite'] = ($tableau['visibilite'])
		? kilometre2mile($tableau['visibilite'])
		: '';
	$tableau['pression'] = ($tableau['pression'])
		? millibar2inch($tableau['pression'])
		: '';
	$tableau['precipitation'] = ($tableau['precipitation'])
		? millimetre2inch($tableau['precipitation'])
		: '';
}


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
function etat2resume_accuweather(&$tableau, $configuration) {

	if ($tableau['code_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service aucun indicateur n'est disponible
		// -> on utilise l'identfiant jour:nuit fourni par le service
		if ($tableau['jour_meteo']) {
			// C'est le jour
			$tableau['periode'] = 0;
		} else {
			// C'est la nuit
			$tableau['periode'] = 1;
		}

		// Détermination du résumé à afficher.
		// Depuis la 3.4.6 on affiche plus que le résumé natif de chaque service car les autres services
		// que weather.com possèdent de nombreuses traductions qu'il convient d'utiliser.
		// Pour éviter de modifier la structure de données, on conserve donc desc_meteo et resume même si
		// maintenant ces deux données coincident toujours.
		$tableau['resume'] = ucfirst($tableau['desc_meteo']);

		// Determination de l'icone qui sera affiché.
		// -- on stocke le code afin de le fournir en alt dans la balise img
		$tableau['icone'] = array();
		$tableau['icone']['code'] = $tableau['code_meteo'];
		// -- on calcule le chemin complet de l'icone.
		if ($configuration['condition'] == $configuration['alias']) {
			// On affiche l'icône natif fourni par le service et désigné par son url
			// en faisant une copie locale dans IMG/.
			include_spip('inc/distant');
			$tableau['icone']['source'] = copie_locale($tableau['icon_meteo']);
		} else {
			include_spip('inc/rainette_normaliser');
			if ($configuration['condition'] == "{$configuration['alias']}_local") {
				// On affiche un icône d'un thème local compatible avec AccuWeather.
				$chemin = icone_local_normaliser(
					"{$tableau['code_meteo']}.png",
					$configuration['alias'],
					$configuration['theme_local']
				);
			} else {
				// On affiche l'icône correspondant au code météo transcodé dans le système weather.com.
				$chemin = icone_weather_normaliser(
					$tableau['code_meteo'],
					$configuration['theme_weather'],
					$configuration['transcodage_weather'],
					$tableau['periode']
				);
			}
			include_spip('inc/utils');
			$tableau['icone']['source'] = find_in_path($chemin);
		}
	}
}
