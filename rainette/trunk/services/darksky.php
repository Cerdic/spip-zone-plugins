<?php
/**
 * Ce fichier contient la configuration et l'ensemble des fonctions implémentant le service Dark Sky (darksky).
 * Ce service est capable de fournir des données au format JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\WSTACK
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_DARKSKY_URL_BASE')) {
	/**
	 * URL de base (endpoint) des requêtes au service APIXU.
	 */
	define('_RAINETTE_DARKSKY_URL_BASE', 'https://api.darksky.net');
}

// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_darksky_config']['service'] = array(
	'alias'   => 'darksky',
	'nom'     => 'Dark Sky',
	'actif'   => true,
	'credits' => array(
		'titre' => 'Powered by Dark Sky',
		'logo'  => '',
		'lien'  => 'https://darksky.net/poweredby/',
	),
	'termes'         => array(
		'titre' => 'Terms of Service',
		'lien'  => 'https://darksky.net/dev/docs/terms'
	),
	'enregistrement' => array(
		'titre'      => 'Register',
		'lien'       => 'https://darksky.net/dev/register',
		'taille_cle' => 32
	),
	'offres'         => array(
		'titre'   => 'Dead Simple Pricing',
		'lien'    => 'https://darksky.net/dev',
		'limites' => array(
			'day' => 1000
		),
	),
	'langues' => array(
		'disponibles' => array(
			'ar'          => 'ar',
			'az'          => 'az',
			'be'          => 'be',
			'bg'          => 'bg',
			'bn'          => 'bn',
			'bs'          => 'bs',
			'ca'          => 'ca',
			'cs'          => 'cs',
			'da'          => 'da',
			'de'          => 'de',
			'el'          => 'el',
			'en'          => 'en',
			'eo'          => 'eo',
			'es'          => 'es',
			'et'          => 'et',
			'fi'          => 'fi',
			'fr'          => 'fr',
			'he'          => 'he',
			'hi'          => 'hi',
			'hr'          => 'hr',
			'hu'          => 'hu',
			'id'          => 'id',
			'is'          => 'is',
			'it'          => 'it',
			'ja'          => 'ja',
			'ka'          => 'ka',
			'kn'          => 'kn',
			'ko'          => 'ko',
			'kw'          => '',
			'lv'          => 'lv',
			'ml'          => 'ml',
			'mr'          => 'mr',
			'nb'          => 'nb',
			'nl'          => 'nl',
			'no'          => 'no',
			'pa'          => 'pa',
			'pl'          => 'pl',
			'pt'          => 'pt',
			'ro'          => 'ro',
			'ru'          => 'ru',
			'sk'          => 'sk',
			'sl'          => 'sl',
			'sr'          => 'sr',
			'sv'          => 'sv',
			'ta'          => 'ta',
			'te'          => 'te',
			'tet'         => '',
			'tr'          => 'tr',
			'uk'          => 'uk',
			'ur'          => 'ur',
			'x-pig-latin' => '',
			'zh'          => 'zh',
			'zh_tw'       => 'zh_tw',
		),
		'defaut'      => 'en'
	),
	'defauts' => array(
		'inscription'   => '',
		'unite'         => 'm',
		'condition'     => 'darksky_local',
		'theme'         => '',
		'theme_local'   => 'original',
		'theme_weather' => 'sticker',
	),
	// Dark Sky ne gère pas le jour et la nuit pour les résumés ou icones :
	// - de fait, on indique les mêmes codes pour le jour et la nuit
	'transcodage_weather' => array(
		'clear-day'           => array(32, 32),
		'clear-night'         => array(31, 31),
		'cloudy'              => array(26, 26),
		'fog'                 => array(20, 20),
		'partly-cloudy-day'   => array(30, 30),
		'partly-cloudy-night' => array(29, 29),
		'rain'                => array(11, 11),
		'sleet'               => array(18, 18),
		'snow'                => array(16, 16),
		'wind'                => array(24, 24),
		'hail'                => array(17, 17), // for future use
		'thunderstorm'        => array(4, 4), // for future use
		'tornado'             => array(0, 0)  // for future use
	)
);

// Configuration des données fournies par le service weatherstack pour le mode 'infos' en format JSON.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_darksky_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array(),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array()),
		'pays'      => array('cle' => array()),
		'pays_iso2' => array('cle' => array()),
		'region'    => array('cle' => array()),
		// Coordonnées
		'longitude' => array('cle' => array('longitude')),
		'latitude'  => array('cle' => array('latitude')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service weatherstack pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_darksky_config']['conditions'] = array(
	'periode_maj' => 10800,
	'format_flux' => 'json',
	'cle_base'    => array('currently'),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('time')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('temperature')),
		'temperature_ressentie' => array('cle' => array('apparentTemperature')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('windSpeed')),
		'angle_vent'            => array('cle' => array('windBearing')),
		'direction_vent'        => array('cle' => array()),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array()),
		'humidite'              => array('cle' => array('humidity')),
		'point_rosee'           => array('cle' => array('dewPoint')),
		'pression'              => array('cle' => array('pressure')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('visibility')),
		'indice_uv'             => array('cle' => array('uvIndex')),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('icon')),
		'icon_meteo'            => array('cle' => array('icon')),
		'desc_meteo'            => array('cle' => array('summary')),
		'trad_meteo'            => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service weatherstack pour le mode 'conditions'.
// -- L'API fournit 10 jours de prévisions avec une périodicité systématique de 1h.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_darksky_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 10),
		//		1 => array('max_jours' => 10)
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 14400,
	'format_flux'        => 'json',
	'cle_base'           => array('daily', 'data'),
	'cle_heure'          => array(),
	'structure_heure'    => false,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('time')),
		'heure'                => array('cle' => array('time')),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array('sunriseTime')),
		'coucher_soleil'       => array('cle' => array('sunsetTime')),
		// Températures
		'temperature'          => array('cle' => array()),
		'temperature_max'      => array('cle' => array('temperatureMax')),
		'temperature_min'      => array('cle' => array('temperatureMin')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('windSpeed')),
		'angle_vent'           => array('cle' => array('windBearing')),
		'direction_vent'       => array('cle' => array()),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array()),
		'precipitation'        => array('cle' => array()),
		'humidite'             => array('cle' => array('humidity')),
		'point_rosee'          => array('cle' => array('dewPoint')),
		'pression'             => array('cle' => array('pressure')),
		'visibilite'           => array('cle' => array('visibility')),
		'indice_uv'            => array('cle' => array('uvIndex')),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('icon')),
		'icon_meteo'           => array('cle' => array('icon')),
		'desc_meteo'           => array('cle' => array('summary')),
		'trad_meteo'           => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service weatherstack en cas d'erreur.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_darksky_config']['erreurs'] = array(
	'cle_base' => array(),
	'donnees'  => array(
		// Erreur
		'code'    => array('cle' => array('code')),
		'message' => array('cle' => array('error')),
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
 *                     Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 *                     La périodicité n'est pas nécessaire car la configuration est indifférente à ce paramètre.
 *
 * @return array
 *               Le tableau des données de configuration communes au service et propres au type de données demandé.
 */
function darksky_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dite
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_darksky_config'][$mode], $GLOBALS['rainette_darksky_config']['service']);

	return $config;
}

/**
 * Construit l'url de la requête correspondant au lieu, au type de données et à la configuration utilisateur
 * du service (par exemple, le code d'inscription, le format des résultats...).
 *
 * @api
 *
 * @uses langue2code_darksky()
 *
 * @param string $lieu
 *                              Lieu pour lequel on acquiert les données météorologiques.
 * @param string $mode
 *                              Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 * @param int    $periodicite
 *                              La périodicité horaire des prévisions :
 *                              - `24`, `12`, `6`, `3` ou `1`, pour le mode `previsions`
 *                              - `0`, pour les modes `conditions` et `infos`
 * @param array  $configuration
 *                              Configuration complète du service, statique et utilisateur.
 *
 * @return string
 *                URL complète de la requête.
 */
function darksky_service2url($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	include_spip('inc/rainette_normaliser');
	$code_langue = langue_determiner($configuration);

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays, le format latitude,longitude et le format adresse IP.
	// Néanmoins, la query a toujours la même forme; il n'est donc pas nécessaire de gérer le format.
	$lieu_normalise = lieu_normaliser($lieu);

	// Exclusion des blocs inutiles en réponse à la requête.
	$exclusions = 'minutely,hourly,alerts,flags,';
	if ($mode == 'previsions') {
		$exclusions .= 'currently';
	} else {
		$exclusions .= 'daily';
	}

	$url = _RAINETTE_DARKSKY_URL_BASE
		. '/forecast'
		. "/{$configuration['inscription']}"
		. "/${lieu_normalise}"
		. "?lang=${code_langue}"
		. '&units=' . ($configuration['unite'] == 'm' ? 'si' : 'us')
		. "&exclude=${exclusions}";

	return $url;
}

/**
 * @param array $erreur
 *
 * @return bool
 */
function darksky_erreur_verifier($erreur) {

	// Initialisation
	$est_erreur = false;

	// Une erreur est toujours décrite par un code et un message.
	if (!empty($erreur['code']) and !empty($erreur['error'])) {
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
 *                             Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *                             par le service.
 * @param array $configuration
 *                             Configuration complète du service, statique et utilisateur.
 *
 * @return array
 *               Tableau standardisé des conditions météorologiques complété par les données spécifiques
 *               du service.
 */
function darksky_complement2conditions($tableau, $configuration) {
	if ($tableau) {
		// Calcul de la direction du vent (16 points).
		include_spip('inc/rainette_convertir');
		$tableau['direction_vent'] = angle2direction($tableau['angle_vent']);
		// On convertit aussi l'humidité en pourcentage car elle est fournie en float entre 0 et 1.
		$tableau['humidite'] = 100 * $tableau['humidite'];

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_darksky($tableau, $configuration);
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
 *                             Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *                             par le service.
 * @param array $configuration
 *                             Configuration complète du service, statique et utilisateur.
 * @param int   $index_periode
 *                             Index où trouver et ranger les données. Cet index n'est pas utilisé pour les conditions
 *
 * @return array
 *               Tableau standardisé des conditions météorologiques complété par les données spécifiques
 *               du service.
 */
function darksky_complement2previsions($tableau, $configuration, $index_periode) {
	if (($tableau) and ($index_periode > -1)) {
		// Calcul de la direction du vent (16 points).
		include_spip('inc/rainette_convertir');
		$tableau['direction_vent'] = angle2direction($tableau['angle_vent']);
		// On convertit aussi l'humidité en pourcentage car elle est fournie en float entre 0 et 1.
		$tableau['humidite'] = 100 * $tableau['humidite'];
		// On calcule aussi le risque UV car celui-ci n'est pas calculé systématiquement pour les prévisions car
		// très rare.
		$tableau['risque_uv'] = indice2risque_uv($tableau['indice_uv']);

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_darksky($tableau, $configuration);
	}

	return $tableau;
}

/**
 * ---------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont des utilitaires uniquement appelées par les fonctions de l'API
 * ---------------------------------------------------------------------------------------------.
 *
 * @param mixed $tableau
 */


/**
 * Calcule les états en fonction des états météorologiques natifs fournis par le service.
 *
 * @internal
 *
 * @param array $tableau
 *                             Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *                             par le service. Le tableau est mis à jour et renvoyé à l'appelant.
 * @param array $configuration
 *                             Configuration complète du service, statique et utilisateur.
 *
 * @return void
 */
function etat2resume_darksky(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit. Pour ce service aucun indicateur n'est disponible.
		// -> on ne se sert cependant pas de cet indicateur pour l'icone qui lui est commun.
		if (strpos($tableau['icon_meteo'], '-night') === false) {
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

		// -- on calcule le chemin complet de l'icone. Le service ne founit pas d'icone via l'API.
		include_spip('inc/rainette_normaliser');
		if ($configuration['condition'] == "{$configuration['alias']}_local") {
			// On affiche un icône d'un thème local compatible avec Dark Sky.
			// Les icônes sont rangés dans themes/$service/$theme_local/. Pas de distinction en fonction de
			// la période.
			$chemin = icone_local_normaliser(
				"{$tableau['icon_meteo']}.png",
				$configuration['alias'],
				$configuration['theme_local']
			);
		} else {
			// On affiche l'icône correspondant au code météo transcodé dans le système weather.com.
			$chemin = icone_weather_normaliser(
				$tableau['code_meteo'],
				$configuration['theme_weather'],
				$configuration['transcodage_weather']
			);
		}
		include_spip('inc/utils');
		$tableau['icone']['source'] = find_in_path($chemin);
	}
}
