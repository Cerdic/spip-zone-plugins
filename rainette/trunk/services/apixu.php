<?php
/**
 * Ce fichier contient la configuration et l'ensemble des fonctions implémentant le service APIXU (apixu).
 * Ce service est capable de fournir des données au format XML ou JSON. Néanmoins, l'API actuelle du plugin utilise
 * uniquement le format JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\APIXU
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_APIXU_URL_BASE')) {
	define('_RAINETTE_APIXU_URL_BASE', 'https://api.apixu.com/v1');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_apixu_config']['service'] = array(
	'alias'   => 'apixu',
	'nom'     => 'APIXU',
	'credits' => array(
		'titre' => 'APIXU',
		'logo'  => 'apixu.png',
		'lien'  => 'https://www.apixu.com/',
	),
	'termes'         => array(
		'titre' => 'Terms and Conditions',
		'lien' => 'https://www.apixu.com/terms.aspx'
	),
	'enregistrement' => array(
		'titre' => 'Signup',
		'lien' => 'https://www.apixu.com/signup.aspx',
		'taille_cle' => 32
	),
	'offres'         => array(
		'titre' => 'Pricing',
		'lien' => 'https://www.apixu.com/pricing.aspx',
		'limites' => array(
			'month' => 5000
		),
	),
	'langues' => array(
		'disponibles' => array(
			'ar'    => 'ar',
			'bg'    => 'bg',
			'bn'    => 'bn',
			'cs'    => 'cs',
			'da'    => 'da',
			'de'    => 'de',
			'el'    => 'el',
			'en'    => 'en',
			'es'    => 'es',
			'fi'    => 'fi',
			'fr'    => 'fr',
			'hi'    => 'hi',
			'hu'    => 'hu',
			'it'    => 'it',
			'ja'    => 'ja',
			'jv'    => 'jv',
			'ko'    => 'ko',
			'mr'    => 'mr',
			'nl'    => 'nl',
			'pa'    => 'pa',
			'pl'    => 'pl',
			'pt'    => 'pt',
			'ro'    => 'ro',
			'ru'    => 'ru',
			'si'    => 'si',
			'sk'    => 'sk',
			'sr'    => 'sr',
			'sv'    => 'sv',
			'ta'    => 'ta',
			'te'    => 'te',
			'tr'    => 'tr',
			'uk'    => 'uk',
			'ur'    => 'ur',
			'vi'    => 'vi',
			'zh'    => 'zh',
			'zh_tw' => 'zh_tw',
			'zu'    => 'zu',
		),
		'defaut'      => 'en'
	),
	'defauts' => array(
		'inscription' => '',
		'unite'       => 'm',
		'condition'   => 'apixu',
		'theme'       => '',
	)
);

// Configuration des données fournies par le service apixu pour le mode 'infos' en format JSON.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_apixu_config']['infos'] = array(
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

// Configuration des données fournies par le service apixu pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_apixu_config']['conditions'] = array(
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
		'indice_uv'             => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('condition', 'code')),
		'icon_meteo'            => array('cle' => array('condition', 'icon')),
		'desc_meteo'            => array('cle' => array('condition', 'text')),
		'trad_meteo'            => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service apixu pour le mode 'conditions'.
// -- L'API fournit 10 jours de prévisions avec une périodicité systématique de 1h.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_apixu_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 10),
		//		1 => array('max_jours' => 10)
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
		'heure'                => array('cle' => array('time')),
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
		'indice_uv'            => array('cle' => array('day', 'uv')),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('day', 'condition', 'code')),
		'icon_meteo'           => array('cle' => array('day', 'condition', 'icon')),
		'desc_meteo'           => array('cle' => array('day', 'condition', 'text')),
		'trad_meteo'           => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service APIXU en cas d'erreur.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_apixu_config']['erreurs'] = array(
	'cle_base' => array('error'),
	'donnees'  => array(
		// Erreur
		'code'    => array('cle' => array('code')),
		'message' => array('cle' => array('message')),
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
function apixu_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dite
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_apixu_config'][$mode], $GLOBALS['rainette_apixu_config']['service']);

	return $config;
}


/**
 * Construit l'url de la requête correspondant au lieu, au type de données et à la configuration utilisateur
 * du service (par exemple, le code d'inscription, le format des résultats...).
 *
 * @api
 * @uses langue2code_apixu()
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
function apixu_service2url($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	include_spip('inc/rainette_normaliser');
	$code_langue = langue_determiner($configuration);

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays, le format latitude,longitude et le format adresse IP.
	// Néanmoins, la query a toujours la même forme; il n'est donc pas nécessaire de gérer le format.
	$lieu_normalise = lieu_normaliser($lieu);

	$url = _RAINETTE_APIXU_URL_BASE;

	if ($mode == 'previsions') {
		$url .= "/forecast.{$configuration['format_flux']}";
	} else {
		$url .= "/current.{$configuration['format_flux']}";
	}

	$url .= '?key=' . $configuration['inscription']
			. '&lang=' . $code_langue
			. '&q=' . $lieu_normalise;

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
function apixu_erreur_verifier($erreur) {

	// Initialisation
	$est_erreur = false;

	// Une erreur est toujours décrite par un code et un message.
	if (!empty($erreur['code']) and !empty($erreur['message'])) {
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
function apixu_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_apixu($tableau, $configuration);
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
function apixu_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {
		// Convertir les informations exprimées en système métrique dans le systeme US si la
		// configuration le demande
		if ($configuration['unite'] == 's') {
			metrique2imperial_apixu($tableau);
		}

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_apixu($tableau, $configuration);
	}

	return $tableau;
}


// ---------------------------------------------------------------------------------------------
// Les fonctions qui suivent sont des utilitaires utilisés uniquement appelées par les fonctions
// de l'API.
// PACKAGE SPIP\RAINETTE\APIXU\OUTILS
// ---------------------------------------------------------------------------------------------

/**
 * @param array $tableau
 *
 * @return void
 */
function metrique2imperial_apixu(&$tableau) {
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


function etat2resume_apixu(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service aucun indicateur n'est disponible
		// -> on utilise le nom de l'icone qui contient l'indication "night" pour la nuit
		$icone = basename($tableau['icon_meteo']);
		if (strpos($icone, '/night/') === false) {
			// C'est le jour
			$tableau['periode'] = 0;
		} else {
			// C'est la nuit
			$tableau['periode'] = 1;
		}

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		if ($configuration['condition'] == $configuration['alias']) {
			// On affiche les conditions natives fournies par le service.
			// Pour le resume, apixu fournit la traduction dans un item différent que pour les autres services.
			// Cet item est stocké dans 'trad_meteo'.
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$tableau['icone']['url'] = copie_locale($tableau['icon_meteo']);
			$tableau['resume'] = ucfirst($tableau['desc_meteo']);
		} else {
			// On affiche les conditions traduites dans le système weather.com
			$meteo = meteo_apixu2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}
}

// TODO : à revoir complètement
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
 function meteo_apixu2weather($meteo, $periode = 0) {
	static $apixu2weather = array(
		395 => array(41, 46),
		392 => array(41, 46),
		389 => array(38, 47),
		386 => array(37, 47),
		377 => array(6, 6),
		374 => array(6, 6),
		371 => array(14, 14),
		368 => array(13, 13),
		365 => array(6, 6),
		362 => array(6, 6),
		359 => array(11, 11),
		356 => array(11, 11),
		353 => array(9, 9),
		350 => array(18, 18),
		338 => array(16, 16),
		335 => array(16, 16),
		332 => array(14, 14),
		329 => array(14, 14),
		326 => array(13, 13),
		323 => array(13, 13),
		320 => array(18, 18),
		317 => array(18, 18),
		314 => array(8, 8),
		311 => array(8, 8),
		308 => array(40, 40),
		305 => array(39, 45),
		302 => array(11, 11),
		299 => array(39, 45),
		296 => array(9, 9),
		293 => array(9, 9),
		284 => array(10, 10),
		281 => array(9, 9),
		266 => array(9, 9),
		263 => array(9, 9),
		260 => array(20, 20),
		248 => array(20, 20),
		230 => array(16, 16),
		227 => array(15, 15),
		200 => array(38, 47),
		185 => array(10, 10),
		182 => array(18, 18),
		179 => array(16, 16),
		176 => array(40, 49),
		143 => array(20, 20),
		122 => array(26, 26),
		119 => array(28, 27),
		116 => array(30, 29),
		113 => array(32, 31)
	);

	$icone = 'na';
	if (array_key_exists($meteo, $apixu2weather)) {
		$icone = strval($apixu2weather[$meteo][$periode]);
	}

	return $icone;
}
