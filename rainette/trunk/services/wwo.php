<?php
/**
 * Ce fichier contient la configuration et l'ensemble des fonctions implémentant le service World Weather Online (wwo).
 * Ce service est capable de fournir des données au format XML ou JSON. Néanmoins, l'API actuelle du plugin utilise
 * uniquement le format JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\WWO
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_WWO_URL_BASE')) {
	/**
	 * URL de base (endpoint) des requêtes au service World Weather Online.
	 */
	define('_RAINETTE_WWO_URL_BASE', 'http://api.worldweatheronline.com/premium/v1/weather.ashx');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_wwo_config']['service'] = array(
	'alias'   => 'wwo',
	'nom'     => 'World Weather Online',
	'credits' => array(
		'titre'       => 'Free local weather content provider',
		'logo'        => null,
		'lien'        => 'http://www.worldweatheronline.com/',
	),
	'termes'         => array(
		'titre' => 'API Terms and Conditions',
		'lien' => 'https://developer.worldweatheronline.com/api/api-t-and-c.aspx'
	),
	'enregistrement' => array(
		'titre' => 'Signup',
		'lien' => 'https://developer.worldweatheronline.com/signup.aspx',
		'taille_cle' => 32
	),
	'offres'         => array(
		'titre' => 'Premium API Pricing',
		'lien' => 'https://developer.worldweatheronline.com/api/pricing.aspx',
		'limites' => array(
			'day'         => 500
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
		'inscription'   => '',
		'unite'         => 'm',
		'condition'     => 'wwo',
		'theme'         => '',
		'theme_local'   => 'original',
		'theme_weather' => 'sticker',
	),
	// @link http://plugins.trac.wordpress.org/browser/weather-and-weather-forecast-widget/trunk/gg_funx_.php
	// Transcodage issu du plugin Wordpress weather forecast.
	// TODO : a revoir, index ok, idem apixu
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

// Configuration des données fournies par le service wwo pour le mode 'infos' en format JSON.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array('data', 'nearest_area', 0),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('areaName', 0, 'value')),
		'pays'      => array('cle' => array('country', 0, 'value')),
		'pays_iso2' => array('cle' => array()),
		'region'    => array('cle' => array('region', 0, 'value')),
		// Coordonnées
		'longitude' => array('cle' => array('longitude')),
		'latitude'  => array('cle' => array('latitude')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service wwo pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['conditions'] = array(
	'periode_maj' => 10800,
	'format_flux' => 'json',
	'cle_base'    => array('data', 'current_condition', 0),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('localObsDateTime')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('temp_'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		'temperature_ressentie' => array('cle' => array('FeelsLike'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('windspeed'), 'suffixe_unite' => array('id_cle' => 0, 'm' => 'Kmph', 's' => 'Miles')),
		'angle_vent'            => array('cle' => array('winddirDegree')),
		'direction_vent'        => array('cle' => array('winddir16Point')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array('precipMM')),
		'humidite'              => array('cle' => array('humidity')),
		'point_rosee'           => array('cle' => array()),
		'pression'              => array('cle' => array('pressure')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('visibility')),
		'indice_uv'             => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('weatherCode')),
		'icon_meteo'            => array('cle' => array('weatherIconUrl', 0, 'value')),
		'desc_meteo'            => array('cle' => array('weatherDesc', 0, 'value')),
		'trad_meteo'            => array('cle' => array('lang_', 0, 'value'), 'suffixe_langue' => array('id_cle' => 0)),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service wwo pour le mode 'conditions'.
// -- L'API Premium fournit 15 jours de prévisions.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 15),
		12 => array('max_jours' => 15),
		6  => array('max_jours' => 15),
		3  => array('max_jours' => 15),
		1  => array('max_jours' => 15)
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
		'indice_uv'            => array('cle' => array('uvIndex')),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('weatherCode')),
		'icon_meteo'           => array('cle' => array('weatherIconUrl', 0, 'value')),
		'desc_meteo'           => array('cle' => array('weatherDesc', 0, 'value')),
		'trad_meteo'           => array('cle' => array('lang_', 0, 'value'), 'suffixe_langue' => array('id_cle' => 0)),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service WWO en cas d'erreur.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['erreurs'] = array(
	'cle_base'    => array('data', 'error', 0),
	'donnees'     => array(
		// Erreur
		'code'     => array('cle' => array()),
		'message'  => array('cle' => array('msg')),
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
function wwo_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dite
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_wwo_config'][$mode], $GLOBALS['rainette_wwo_config']['service']);

	return $config;
}


/**
 * Construit l'url de la requête correspondant au lieu, au type de données et à la configuration utilisateur
 * du service (par exemple, le code d'inscription, le format des résultats...).
 *
 * @api
 * @uses langue2code_wwo()
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
function wwo_service2url($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	include_spip('inc/rainette_normaliser');
	$code_langue = langue_determiner($configuration);

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays, le format latitude,longitude et le format adresse IP.
	// Néanmoins, la query a toujours la même forme; il n'est donc pas nécessaire de gérer le format.
	$lieu_normalise = lieu_normaliser($lieu);

	$url = _RAINETTE_WWO_URL_BASE
		   . '?key=' . $configuration['inscription']
		   . '&format=' . $configuration['format_flux']
		   . '&extra=localObsTime'
		   . '&lang=' . $code_langue
		   . '&q=' . $lieu_normalise;

	if ($mode == 'infos') {
		$url .= '&includeLocation=yes&cc=no&fx=no';
	} elseif ($mode == 'conditions') {
		$url .= '&cc=yes&fx=no';
	} else {
		$url .= '&cc=no&fx=yes'
				. '&num_of_days=' . $configuration['periodicites'][$periodicite]['max_jours']
				. '&tp=' . strval($periodicite);
	}

	return $url;
}


/**
 * @param array $erreur
 *
 * @return bool
 */
function wwo_erreur_verifier($erreur) {

	// Initialisation
	$est_erreur = false;

	// Une erreur est uniquement décrite par un message.
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
function wwo_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Convertir les informations exprimées en système métrique dans le systeme US si la
		// configuration le demande
		if ($configuration['unite'] == 's') {
			metrique2imperial_wwo($tableau);
		}

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_wwo($tableau, $configuration);
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
function wwo_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {
		// Convertir les informations exprimées en système métrique dans le systeme US si la
		// configuration le demande
		if ($configuration['unite'] == 's') {
			metrique2imperial_wwo($tableau);
		}

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_wwo($tableau, $configuration);
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
function metrique2imperial_wwo(&$tableau) {
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
function etat2resume_wwo(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service aucun indicateur n'est disponible
		// -> on utilise le nom de l'icone qui contient l'indication "night" pour la nuit
		$icone = basename($tableau['icon_meteo']);
		if (strpos($icone, '_night') === false) {
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
		// Pour wwo, la description est dans trad_meteo et non dans desc_meteo.
		$tableau['resume'] = ucfirst($tableau['trad_meteo']);

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
				// On affiche un icône d'un thème local compatible avec WWO.
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
