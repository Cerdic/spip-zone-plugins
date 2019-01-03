<?php
/**
 * Ce fichier contient la configuration et l'ensemble des fonctions implémentant le service Weatherbit.io (weatherbit).
 * Ce service est capable de fournir des données au format JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\WEATHERBIT
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_WEATHERBIT_URL_BASE')) {
	/**
	 * URL de base (endpoint) des requêtes au service Weatherbit.io.
	 */
	define('_RAINETTE_WEATHERBIT_URL_BASE', 'https://api.weatherbit.io/v2.0/');
}
if (!defined('_RAINETTE_WEATHERBIT_URL_BASE_ICONE')) {
	/**
	 * URL de base des icônes fournis par le service Weatherbit.io.
	 */
	define('_RAINETTE_WEATHERBIT_URL_BASE_ICONE', 'https://www.weatherbit.io/static/img/icons/');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_weatherbit_config']['service'] = array(
	'alias'   => 'weatherbit',
	'nom'     => 'Weatherbit.io',
	'credits' => array(
		'titre' => 'Weatherbit API',
		'logo'  => '',
		'lien'  => 'https://www.weatherbit.io/',
	),
	'termes'         => array(
		'titre' => 'Terms and Conditions',
		'lien' => 'https://www.weatherbit.io/terms'
	),
	'enregistrement' => array(
		'titre' => 'Sign up for the Weatherbit API!',
		'lien' => 'https://www.weatherbit.io/account/create',
		'taille_cle' => 32
	),
	'offres'         => array(
		'titre' => 'Affordable Weather API plans',
		'lien' => 'https://www.weatherbit.io/pricing',
		'limites' => array(
			'hour' => 75
		),
	),
	'langues' => array(
		'disponibles' => array(
			'ar'    => 'ar',
			'az'    => 'az',
			'be'    => 'be',
			'bg'    => 'bg',
			'bs'    => 'bs',
			'ca'    => 'ca',
			'cz'    => 'cs',
			'da'    => 'da',
			'de'    => 'de',
			'el'    => 'el',
			'en'    => 'en',
			'et'    => 'et',
			'fi'    => 'fi',
			'fr'    => 'fr',
			'hr'    => 'hr',
			'hu'    => 'hu',
			'id'    => 'id',
			'is'    => 'is',
			'it'    => 'it',
			'kw'    => '',
			'lt'    => 'lt',
			'nb'    => 'nb',
			'nl'    => 'nl',
			'pl'    => 'pl',
			'pt'    => 'pt',
			'ro'    => 'ro',
			'ru'    => 'ru',
			'sk'    => 'sk',
			'sl'    => 'sl',
			'sr'    => 'sr',
			'sv'    => 'sv',
			'tr'    => 'tr',
			'zh'    => 'zh',
			'zh_tw' => 'zh_tw',
		),
		'defaut'      => 'en'
	),
	'defauts' => array(
		'inscription'   => '',
		'unite'         => 'm',
		'condition'     => 'weatherbit',
		'theme'         => '',
		'theme_local'   => 'original',
		'theme_weather' => 'sticker',
	),
	// TODO : tout à revoir
	'transcodage_weather' => array(
	)
);

// Configuration des données fournies par le service weatherbit pour le mode 'infos' en format JSON.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weatherbit_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array('data', 0),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('city_name')),
		'pays'      => array('cle' => array('')),
		'pays_iso2' => array('cle' => array('country_code')),
		'region'    => array('cle' => array('')),
		// Coordonnées
		'longitude' => array('cle' => array('lon')),
		'latitude'  => array('cle' => array('lat')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service weatherbit pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weatherbit_config']['conditions'] = array(
	'periode_maj' => 10800,
	'format_flux' => 'json',
	'cle_base'    => array('data', 0),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('ob_time')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('temp')),
		'temperature_ressentie' => array('cle' => array('app_temp')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('wind_spd')),
		'angle_vent'            => array('cle' => array('wind_dir')),
		'direction_vent'        => array('cle' => array('wind_cdir')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array('precip')),
		'humidite'              => array('cle' => array('rh')),
		'point_rosee'           => array('cle' => array('dewpt')),
		'pression'              => array('cle' => array('pres')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('vis')),
		'indice_uv'             => array('cle' => array('uv')),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('weather', 'code')),
		'icon_meteo'            => array('cle' => array('weather', 'icon')),
		'desc_meteo'            => array('cle' => array('weather', 'description')),
		'trad_meteo'            => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
		// TODO : il existe l'indicateur jour/nuit directement renvoyé par le service (pod).
	),
);

// Configuration des données fournies par le service weatherbit pour le mode 'previsions'.
// -- L'API fournit 16 jours de prévisions avec une périodicité systématique de 24h.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weatherbit_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 16),
		1 => array('max_jours' => 2),
		3 => array('max_jours' => 5)
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 14400,
	'format_flux'        => 'json',
	'cle_base'           => array('data'),
	'cle_heure'          => array(),
	'structure_heure'    => false,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('datetime')),
		'heure'                => array('cle' => array()),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array()),
		'coucher_soleil'       => array('cle' => array()),
		// Températures
		'temperature'          => array('cle' => array('temp')),
		'temperature_max'      => array('cle' => array('max_temp')),
		'temperature_min'      => array('cle' => array('min_temp')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('wind_spd')),
		'angle_vent'           => array('cle' => array('wind_dir')),
		'direction_vent'       => array('cle' => array('wind_cdir')),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array('pop')),
		'precipitation'        => array('cle' => array('precip')),
		'humidite'             => array('cle' => array('rh')),
		'point_rosee'          => array('cle' => array('dewpt')),
		'pression'             => array('cle' => array('pres')),
		'visibilite'           => array('cle' => array('vis')),
		'indice_uv'            => array('cle' => array('uv')),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('weather', 'code')),
		'icon_meteo'           => array('cle' => array('weather', 'icon')),
		'desc_meteo'           => array('cle' => array('weather', 'description')),
		'trad_meteo'           => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
		// TODO : il existe l'indicateur jour/nuit directement renvoyé par le service (pod).
	),
);

// Configuration des données fournies par le service WEATHERBIT en cas d'erreur.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weatherbit_config']['erreurs'] = array(
	'cle_base' => array(),
	'donnees'  => array(
		// Erreur
		'code'    => array('cle' => array()),
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
 *        Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 *        La périodicité n'est pas nécessaire car la configuration est indifférente à ce paramètre.
 *
 * @return array
 *        Le tableau des données de configuration communes au service et propres au type de données demandé.
 */
function weatherbit_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dite
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_weatherbit_config'][$mode], $GLOBALS['rainette_weatherbit_config']['service']);

	return $config;
}


/**
 * Construit l'url de la requête correspondant au lieu, au type de données et à la configuration utilisateur
 * du service (par exemple, le code d'inscription, le format des résultats...).
 *
 * @api
 * @uses langue2code_weatherbit()
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
function weatherbit_service2url($lieu, $mode, $periodicite, $configuration) {

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays, le format latitude,longitude et le format adresse IP.
	$lieu_normalise = lieu_normaliser($lieu, $format_lieu);
	if ($format_lieu == 'adresse_ip') {
		$localisation = "ip=${lieu_normalise}";
	} elseif ($format_lieu == 'latitude_longitude') {
		list($latitude, $longitude) = explode(',', $lieu_normalise);
		$localisation = "lat=${latitude}&lon=${longitude}";
	} else { // Format ville,pays
		$elements = explode(',', $lieu_normalise);
		$localisation = "city={$elements[0]}";
		if (count($elements) == 2) {
			// Le pays est précisé, il faut l'inclure dans un attribut paramètre spécifique 'country'.
			$localisation .= "&country={$elements[1]}";
		}
	}

	// Détermination du paramètre constitutif de la demande
	if ($mode == 'previsions') {
		$demande = 'forecast/';
		if ($periodicite == 24) {
			$demande .= 'daily';
		} elseif ($periodicite == 1) {
			$demande .= 'hourly';
		} else {
			// Forcément 3 heures
			$demande .= '3hourly';
		}
	} else {
		$demande = 'current';
	}

	// Identification de la langue du resume.
	include_spip('inc/rainette_normaliser');
	$code_langue = langue_determiner($configuration);

	$url = _RAINETTE_WEATHERBIT_URL_BASE
		. $demande . '?'
		. $localisation
		. '&lang=' . $code_langue
		. '&units=' . ($configuration['unite'] == 'm' ? 'M' : 'I')
		. '&key=' . $configuration['inscription'];

	return $url;
}


/**
 * @param array $erreur
 *
 * @return bool
 */
function weatherbit_erreur_verifier($erreur) {

	// Initialisation
	$est_erreur = false;

	// Une erreur est toujours décrite par un unique message.
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
function weatherbit_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_weatherbit($tableau, $configuration);
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
function weatherbit_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {
		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_weatherbit($tableau, $configuration);
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
function etat2resume_weatherbit(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icône.
		// TODO : Pour ce service il existe un indicateur qu'il faudra utiliser
		if (substr($tableau['icon_meteo'], -1) == 'd') {
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
			$url = _RAINETTE_WEATHERBIT_URL_BASE_ICONE . '/' . $tableau['icon_meteo'] . '.png';
			$tableau['icone']['source'] = copie_locale($url);
		} else {
			include_spip('inc/rainette_normaliser');
			if ($configuration['condition'] == "{$configuration['alias']}_local") {
				// On affiche un icône d'un thème local compatible avec Weatherbit.
				$chemin = icone_local_normaliser(
					"{$tableau['icon_meteo']}.png",
					$configuration['alias'],
					$configuration['theme_local']);
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
