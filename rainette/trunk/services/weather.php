<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant l'ancien service Weather.com (weather).
 * Ce service fournit des données au format XML uniquement.
 *
 * @package SPIP\RAINETTE\SERVICES\WEATHER
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_WEATHER_URL_BASE')) {
	define('_RAINETTE_WEATHER_URL_BASE', 'http://wxdata.weather.com/wxdata/weather/local/');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_weather_config']['service'] = array(
	'alias'   => 'weather',
	'defauts'        => array(
		'inscription' => '',
		'unite'       => 'm',
		'condition'   => 'weather',
		'theme'       => '',
	),
	'credits'        => array(
		'titre' => null,
		'logo'  => null,
		'lien'  => 'http://www.weather.com/',
	),
	'langues' => array(
		'disponibles' => array(),
		'defaut'      => 'en'
	)
);

// Configuration des données fournies par le service wunderground pour le mode 'infos'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weather_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'xml',
	'cle_base'    => array('children', 'loc', 0, 'children'),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('dnam', 0, 'text')),
		'pays'      => array('cle' => array()),
		'pays_iso2' => array('cle' => array()),
		'region'    => array('cle' => array()),
		// Coordonnées
		'longitude' => array('cle' => array('lon', 0, 'text')),
		'latitude'  => array('cle' => array('lat', 0, 'text')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service weather pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weather_config']['conditions'] = array(
	'periode_maj' => 1800,
	'format_flux' => 'xml',
	'cle_base'    => array('children', 'cc', 0, 'children'),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('lsup', 0, 'text')),
		'station'               => array('cle' => array('obst', 0, 'text')),
		// Températures
		'temperature_reelle'    => array('cle' => array('tmp', 0, 'text')),
		'temperature_ressentie' => array('cle' => array('flik', 0, 'text')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('wind', 0, 'children', 's', 0, 'text')),
		'angle_vent'            => array('cle' => array('wind', 0, 'children', 'd', 0, 'text')),
		'direction_vent'        => array('cle' => array('wind', 0, 'children', 't', 0, 'text')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array()),
		'humidite'              => array('cle' => array('hmid', 0, 'text')),
		'point_rosee'           => array('cle' => array('dewp', 0, 'text')),
		'pression'              => array('cle' => array('bar', 0, 'children', 'r', 0, 'text')),
		'tendance_pression'     => array('cle' => array('bar', 0, 'children', 'd', 0, 'text')),
		'visibilite'            => array('cle' => array('vis', 0, 'text')),
		'indice_uv'             => array('cle' => array('uv', 0, 'children', 'i', 0, 'text')),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('icon', 0, 'text')),
		'icon_meteo'            => array('cle' => array()),
		'desc_meteo'            => array('cle' => array('t', 0, 'text')),
		'trad_meteo'            => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service weather pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_weather_config']['previsions'] = array(
	'periodicites'       => array(
		12 => array('max_jours' => 10)
	),
	'periodicite_defaut' => 12,
	'periode_maj'        => 1800,
	'format_flux'        => 'xml',
	'cle_base'           => array('children', 'dayf', 0, 'children', 'day'),
	'cle_heure'          => array('children', 'part'),
	'structure_heure'    => true,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('attributes', 'dt')),
		'heure'                => array('cle' => array()),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array('children', 'sunr', 0, 'text')),
		'coucher_soleil'       => array('cle' => array('children', 'suns', 0, 'text')),
		// Températures
		'temperature'          => array('cle' => array()),
		'temperature_max'      => array('cle' => array('children', 'hi', 0, 'text')),
		'temperature_min'      => array('cle' => array('children', 'low', 0, 'text')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('children', 'wind', 0, 'children', 's', 0, 'text')),
		'angle_vent'           => array('cle' => array('children', 'wind', 0, 'children', 'd', 0, 'text')),
		'direction_vent'       => array('cle' => array('children', 'wind', 0, 'children', 't', 0, 'text')),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array('children', 'ppcp', 0, 'text')),
		'precipitation'        => array('cle' => array()),
		'humidite'             => array('cle' => array('children', 'hmid', 0, 'text')),
		'point_rosee'          => array('cle' => array()),
		'pression'             => array('cle' => array()),
		'visibilite'           => array('cle' => array()),
		'indice_uv'            => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('children', 'icon', 0, 'text')),
		'icon_meteo'           => array('cle' => array()),
		'desc_meteo'           => array('cle' => array('children', 't', 0, 'text')),
		'trad_meteo'           => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);


/**
 * @param string $mode
 *
 * @return string
 */
function weather_service2configuration($mode) {
	global $rainette_weather_config;
	// On merge la configuration propre au mode et la configuration du service proprement dit
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($rainette_weather_config[$mode], $rainette_weather_config['service']);

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
function weather_service2url($lieu, $mode, $periodicite, $configuration) {


	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays, le format latitude,longitude et le format adresse IP.
	// Néanmoins, la query a toujours la même forme; il n'est donc pas nécessaire de gérer le format.
	include_spip('inc/rainette_normaliser');
	list($lieu_normalise,) = normaliser_lieu($lieu);

	$url = _RAINETTE_WEATHER_URL_BASE
	   . $lieu_normalise
	   . '?unit='
	   . $configuration['unite'];

	if ($mode != 'infos') {
		$url .= ($mode == 'previsions')
			? '&dayf=' . $configuration['periodicites'][$periodicite]['max_jours']
			: '&cc=*';
	}

	return $url;
}


function weather_complement2infos($tableau, $configuration) {

	// Le nom de la ville retournée par le service est sous la forme 'Ville,Région[,Pays]' (Région désigne
	// parfois le département (France) ou l'état (Etats-Unis).
	// Il faut donc répartir la valeur d'index 'ville' dans les index 'ville', 'region' et 'pays' sachant
	// que le pays n'est pas toujours fourni.
	$lieu = explode(',', $tableau['ville']);
	$tableau['ville'] = trim($lieu[0]);
	$tableau['region'] = !empty($lieu[1]) ? trim($lieu[1]) : '';
	$tableau['pays'] = !empty($lieu[2]) ? trim($lieu[2]) : '';

	return $tableau;
}


function weather_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Compléter le tableau standard avec les états météorologiques calculés
		if ($tableau['code_meteo']) {
			// La traduction du resume dans la bonne langue est toujours faite par les fichiers de langue SPIP
			// car l'API ne permet pas de choisir la langue. On ne stocke donc que le code meteo
			etat2resume_weather($tableau, $configuration);
		}
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
function weather_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {
		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_weather($tableau, $configuration);
	}

	return $tableau;
}


function etat2resume_weather(&$tableau, $configuration) {

	if ($tableau['code_meteo']) {
		// A priori la période de nuit commence à 14h et se termine à 5h.
		// Cette donnée n'est pas utile pour les conditions de ce service, on la positionne à null : TODO
		$tableau['periode'] = 0;

		$tableau['icone'] = $tableau['code_meteo'];
		$tableau['resume'] = $tableau['code_meteo'];
	}
}
