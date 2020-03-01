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
	'actif'   => false,
	'credits' => array(
		'titre' => 'Powered by Dark Sky',
		'logo'  => 'darksky.png',
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
		'condition'     => 'darksky',
		'theme'         => '',
		'theme_local'   => 'original',
		'theme_weather' => 'sticker',
	),
	// @link http://plugins.trac.wordpress.org/browser/weather-and-weather-forecast-widget/trunk/gg_funx_.php
	// Transcodage issu du plugin Wordpress weather forecast.
	// TODO : a revoir, index ok, idem que wwo a priori
	'transcodage_weather' => array(
		113 => array(32, 31),
		116 => array(30, 29),
		119 => array(28, 27),
		122 => array(26, 26),
		143 => array(20, 20),
		176 => array(40, 49),
		179 => array(16, 16),
		182 => array(18, 18),
		185 => array(10, 10),
		200 => array(38, 47),
		227 => array(15, 15),
		230 => array(16, 16),
		248 => array(20, 20),
		260 => array(20, 20),
		263 => array(9, 9),
		266 => array(9, 9),
		281 => array(9, 9),
		284 => array(10, 10),
		293 => array(9, 9),
		296 => array(9, 9),
		299 => array(39, 45),
		302 => array(11, 11),
		305 => array(39, 45),
		308 => array(40, 40),
		311 => array(8, 8),
		314 => array(8, 8),
		317 => array(18, 18),
		320 => array(18, 18),
		323 => array(13, 13),
		326 => array(13, 13),
		329 => array(14, 14),
		332 => array(14, 14),
		335 => array(16, 16),
		338 => array(16, 16),
		350 => array(18, 18),
		353 => array(9, 9),
		356 => array(11, 11),
		359 => array(11, 11),
		362 => array(6, 6),
		365 => array(6, 6),
		368 => array(13, 13),
		371 => array(14, 14),
		374 => array(6, 6),
		377 => array(6, 6),
		386 => array(37, 47),
		389 => array(38, 47),
		392 => array(41, 46),
		395 => array(41, 46)
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
		'direction_vent'        => array('cle' => array('')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array('')),
		'humidite'              => array('cle' => array('humidity')),
		'point_rosee'           => array('cle' => array('dewPoint')),
		'pression'              => array('cle' => array('pressure')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('visibility')),
		'indice_uv'             => array('cle' => array('uvIndex')),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('icon')),
		'icon_meteo'            => array('cle' => array('icon', 0)),
		'desc_meteo'            => array('cle' => array('summary', 0)),
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
		'precipitation'        => array('cle' => array('')),
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
	'cle_base' => array('error'),
	'donnees'  => array(
		// Erreur
		'code'  => array('cle' => array('code')),
		'error' => array('cle' => array('info')),
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
		. "exclude=${exclusions}";

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
		// Convertir les informations exprimées en système métrique dans le systeme US si la
		// configuration le demande
		if ($configuration['unite'] == 's') {
			metrique2imperial_darksky($tableau);
		}

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
 * @param array $tableau
 *
 * @return void
 */
function metrique2imperial_darksky(&$tableau) {

	// Seules la température, la température ressentie et la vitesse du vent sont fournies dans
	// les deux systèmes.
	// Etant donnée que les tableaux sont normalisés, ils contiennent toujours les index de chaque
	// donnée météo, il est donc inutile de tester leur existence.
	include_spip('inc/rainette_convertir');
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
		// -> on utilise l'url de l'icone qui contient l'indication "/night/" pour la nuit
		if (strpos($tableau['icon_meteo'], '/night/') === false) {
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
				// On affiche un icône d'un thème local compatible avec APIXU.
				// Les icônes sont rangés dans themes/$service/$theme_local/$periode où periode vaut 'day' ou 'night'.
				// Les icônes APIXU sont les mêmes que ceux de wwo, seuls le code météo change. Néanmoins, le service
				// APIXU renvoi dans la donnée 'icon_meteo' le code wwo que l'on peut utiliser pour construire l'icone.
				$chemin = icone_local_normaliser(
					basename($tableau['icon_meteo']),
					$configuration['alias'],
					$configuration['theme_local'],
					$tableau['periode'] == 0 ? 'day' : 'night'
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
