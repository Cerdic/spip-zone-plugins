<?php
/**
 * Ce fichier contient la configuration et l'ensemble des fonctions implémentant le service WeatherAPI.com (weatherapi).
 * Ce service est capable de fournir des données au format JSON. Ce service ressemble à WWO à ceci près qu'il propose
 * un plan gratuit.
 *
 * @package SPIP\RAINETTE\SERVICES\WEATHERAPI
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_WEATHERAPI_URL_BASE')) {
	/**
	 * URL de base (endpoint) des requêtes au service World Weather Online.
	 */
	define('_RAINETTE_WEATHERAPI_URL_BASE', 'https://api.weatherapi.com/v1');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_weatherapi_config']['service'] = array(
	'alias'   => 'weatherapi',
	'nom'     => 'WeatherAPI',
	'actif'   => true,
	'credits' => array(
		'titre'       => 'WeatherAPI.com',
		'logo'        => null,
		'lien'        => 'https://www.weatherapi.com/',
	),
	'termes'         => array(
		'titre' => 'Terms and Conditions',
		'lien' => 'https://www.weatherapi.com/terms.aspx'
	),
	'enregistrement' => array(
		'titre' => 'Sign Up',
		'lien' => 'https://www.weatherapi.com/signup.aspx',
		'taille_cle' => 30
	),
	'offres'         => array(
		'titre' => 'Pricing',
		'lien' => 'https://www.weatherapi.com/pricing.aspx',
		'limites' => array(
			'month' => 20000
		),
	),
	'langues' => array(
		'disponibles' => array(
			'ar'     => 'ar',
			'bg'     => 'bg',
			'bn'     => 'bn',
			'cs'     => 'cs',
			'da'     => 'da',
			'de'     => 'de',
			'el'     => 'el',
			'en'     => 'en',
			'es'     => 'es',
			'fi'     => 'fi',
			'fr'     => 'fr',
			'hi'     => 'hi',
			'hu'     => 'hu',
			'it'     => 'it',
			'ja'     => 'ja',
			'jv'     => 'jv',
			'ko'     => 'ko',
			'mr'     => 'mr',
			'nl'     => 'nl',
			'pa'     => 'pa',
			'pl'     => 'pl',
			'pt'     => 'pt',
			'ro'     => 'ro',
			'ru'     => 'ru',
			'si'     => 'si',
			'sk'     => 'sk',
			'sr'     => 'sr',
			'sv'     => 'sv',
			'ta'     => 'ta',
			'te'     => 'te',
			'tr'     => 'tr',
			'uk'     => 'uk',
			'ur'     => 'ur',
			'vi'     => 'vi',
			'zh'     => 'zh',
			'zh_cmn' => '',
			'zh_hsn' => '',
			'zh_tw'  => 'zh_tw',
			'zh_wuu' => '',
			'zh_yue' => '',
			'zu'     => 'zu',
		),
		'defaut'      => 'en'
	),
	'defauts' => array(
		'inscription'   => '',
		'unite'         => 'm',
		'condition'     => 'weatherapi',
		'theme'         => '',
		'theme_local'   => 'original',
		'theme_weather' => 'sticker',
	),
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

// Configuration des données fournies par le service weatherapi pour le mode 'infos' en format JSON.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weatherapi_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array('location'),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('name')),
		'pays'      => array('cle' => array('country')),
		'pays_iso2' => array('cle' => array()),
		'region'    => array('cle' => array('region')),
		// Coordonnées
		'longitude' => array('cle' => array('lon')),
		'latitude'  => array('cle' => array('lat')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service weatherapi pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weatherapi_config']['conditions'] = array(
	'periode_maj' => 10800,
	'format_flux' => 'json',
	'cle_base'    => array('current'),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('last_updated')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('temp_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'c', 's' => 'f')),
		'temperature_ressentie' => array('cle' => array('feelslike_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'c', 's' => 'f')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('wind_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'kph', 's' => 'mph')),
		'angle_vent'            => array('cle' => array('wind_degree')),
		'direction_vent'        => array('cle' => array('wind_dir')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array('precip_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'mm', 's' => 'in')),
		'humidite'              => array('cle' => array('humidity')),
		'point_rosee'           => array('cle' => array()),
		'pression'              => array('cle' => array('pressure_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'mb', 's' => 'in')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('vis_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'km', 's' => 'miles')),
		'nebulosite'            => array('cle' => array('cloud')),
		'indice_uv'             => array('cle' => array('uv')),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('condition', 'code')),
		'icon_meteo'            => array('cle' => array('condition', 'icon')),
		'desc_meteo'            => array('cle' => array('condition', 'text')),
		'trad_meteo'            => array('cle' => array()),
		'jour_meteo'            => array('cle' => array('is_day')),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service weatherapi pour le mode 'conditions'.
// -- L'API Premium fournit 15 jours de prévisions.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weatherapi_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 10),
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 14400,
	'format_flux'        => 'json',
	'cle_base'           => array('forecast', 'forecastday'),
	'cle_heure'          => array(),
	'structure_heure'    => false,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('date')),
		'heure'                => array('cle' => array()),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array('astro', 'sunrise')),
		'coucher_soleil'       => array('cle' => array('astro', 'sunset')),
		// Températures
		'temperature'          => array('cle' => array('day', 'avgtemp_'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'c', 's' => 'f')),
		'temperature_max'      => array('cle' => array('day', 'maxtemp_'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'c', 's' => 'f')),
		'temperature_min'      => array('cle' => array('day', 'mintemp_'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'c', 's' => 'f')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('day', 'maxwind_'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'kph', 's' => 'mph')),
		'angle_vent'           => array('cle' => array()),
		'direction_vent'       => array('cle' => array()),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array()),
		'precipitation'        => array('cle' => array('day', 'totalprecip_'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'mm', 's' => 'in')),
		'humidite'             => array('cle' => array('day', 'avghumidity')),
		'point_rosee'          => array('cle' => array()),
		'pression'             => array('cle' => array()),
		'visibilite'           => array('cle' => array('day', 'avgvis_'), 'suffixe_unite' => array('id_cle' => 1, 'm' => 'km', 's' => 'miles')),
		'nebulosite'           => array('cle' => array()),
		'indice_uv'            => array('cle' => array('day', 'uv')),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('day', 'condition', 'code')),
		'icon_meteo'           => array('cle' => array('day', 'condition', 'icon')),
		'desc_meteo'           => array('cle' => array('day', 'condition', 'text')),
		'trad_meteo'           => array('cle' => array()),
		'jour_meteo'           => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service WEATHERAPI en cas d'erreur.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weatherapi_config']['erreurs'] = array(
	'cle_base'    => array('error'),
	'donnees'     => array(
		// Erreur
		'code'     => array('cle' => array('code')),
		'message'  => array('cle' => array('message')),
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
function weatherapi_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dite
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_weatherapi_config'][$mode], $GLOBALS['rainette_weatherapi_config']['service']);

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
function weatherapi_service2url($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	include_spip('inc/rainette_normaliser');
	$code_langue = langue_determiner($configuration);

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays, le format latitude,longitude et le format adresse IP.
	// Néanmoins, la query a toujours la même forme; il n'est donc pas nécessaire de gérer le format.
	$lieu_normalise = lieu_normaliser($lieu);

	// On détermine la méthode de l'API en fonction du mode
	if ($mode == 'previsions') {
		$methode = 'forecast';
	} else {
		$methode = 'current';
	}

	$url = _RAINETTE_WEATHERAPI_URL_BASE
		. '/' . $methode . '.' . $configuration['format_flux']
		. '?key=' . $configuration['inscription']
		. '&lang=' . $code_langue
		. '&q=' . $lieu_normalise;

	// Si on est en mode prévision il faut rajouter le nombre de jours possibles
	if ($mode == 'previsions') {
		$url .= '&days=' . $configuration['periodicites'][$periodicite]['max_jours'];
	}

	return $url;
}


/**
 * @param array $erreur
 *
 * @return bool
 */
function weatherapi_erreur_verifier($erreur) {

	// Initialisation
	$est_erreur = false;

	// Une erreur est uniquement décrite par un message et un code.
	if (!empty($erreur['message'])) {
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
function weatherapi_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_weatherapi($tableau, $configuration);
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
function weatherapi_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {
		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_weatherapi($tableau, $configuration);
	}

	return $tableau;
}


/**
 * ---------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont des utilitaires uniquement appelées par les fonctions de l'API
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
function etat2resume_weatherapi(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icône.
		// - on utilise l'indicateur fourni par le service si il existe sinon l'icone
		if ($tableau['jour_meteo']) {
			$tableau['periode'] = $tableau['jour_meteo'] ? 0 : 1;
		} else {
			$tableau['periode'] = strpos($tableau['icon_meteo'], '/night/') === false ? 0 : 1;
		}

		// Détermination du résumé à afficher.
		// Depuis la 3.4.6 on affiche plus que le résumé natif de chaque service car les autres services
		// que weather.com possèdent de nombreuses traductions qu'il convient d'utiliser.
		// Pour éviter de modifier la structure de données, on conserve donc desc_meteo et resume même si
		// maintenant ces deux données coincident toujours.
		// Pour weatherapi, la description est dans desc_meteo.
		$tableau['resume'] = ucfirst($tableau['desc_meteo']);

		// Determination de l'icone qui sera affiché.
		// -- Traitement particulier de code_meteo qui ne désigne pas l'icone dans ce service : on extrait donc
		//    le code_icone de l'icone lui-même
		$tableau['code_meteo'] = basename($tableau['icon_meteo'], '.png');
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
				// On affiche un icône d'un thème local compatible avec WEATHERAPI.
				$chemin = icone_local_normaliser(
					"{$tableau['code_meteo']}.png",
					$configuration['alias'],
					$configuration['theme_local'],
					$tableau['periode'] == 0 ? 'day' : 'night');
			} else {
				// On affiche l'icône correspondant au code météo transcodé dans le système weather.com.
				$chemin = icone_weather_normaliser(
					$tableau['code_meteo'],
					$configuration['theme_weather'],
					$configuration['transcodage_weather'],
					$tableau['periode']);
			}
			include_spip('inc/utils');
			$tableau['icone']['source'] = find_in_path($chemin);
		}
	}
}
