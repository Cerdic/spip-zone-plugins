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
	 * UEL de base des icônes fournis par le service Wunderground
	 */
	define('_RAINETTE_WUNDERGROUND_URL_BASE_ICONE', 'http://icons.wxug.com/i/c');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_wunderground_config']['service'] = array(
	'defauts'        => array(
		'inscription' => '',
		'unite'       => 'm',
		'condition'   => 'wundergound',
		'theme'       => 'a',
	),
	'credits'        => array(
		'titre' => null,
		'logo'  => 'wunderground-126.png',
		'lien'  => 'http://www.wunderground.com/',
	),
	'langue_service' => 'EN'
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
		'temperature_reelle'    => array('cle' => array('temp_'), 'suffixe' => array('id_cle' => 0, 'm' => 'c', 's' => 'f')),
		'temperature_ressentie' => array('cle' => array('feelslike_'), 'suffixe' => array('id_cle' => 0, 'm' => 'c', 's' => 'f')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('wind_'), 'suffixe' => array('id_cle' => 0, 'm' => 'kph', 's' => 'mph')),
		'angle_vent'            => array('cle' => array('wind_degrees')),
		'direction_vent'        => array('cle' => array()),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array('precip_today_'), 'suffixe' => array('id_cle' => 0, 'm' => 'metric', 's' => 'in')),
		'humidite'              => array('cle' => array('relative_humidity')),
		'point_rosee'           => array('cle' => array('dewpoint_'), 'suffixe' => array('id_cle' => 0, 'm' => 'c', 's' => 'f')),
		'pression'              => array('cle' => array('pressure_'), 'suffixe' => array('id_cle' => 0, 'm' => 'mb', 's' => 'in')),
		'tendance_pression'     => array('cle' => array('pressure_trend')),
		'visibilite'            => array('cle' => array('visibility_'), 'suffixe' => array('id_cle' => 0, 'm' => 'km', 's' => 'mi')),
		'indice_uv'             => array('cle' => array('UV')),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('icon')),
		'icon_meteo'            => array('cle' => array('icon_url')),
		'desc_meteo'            => array('cle' => array('weather')),
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
		'temperature_max'      => array('cle' => array('high', ''), 'suffixe' => array('id_cle' => 1, 'm' => 'celsius', 's' => 'fahrenheit')),
		'temperature_min'      => array('cle' => array('low', ''), 'suffixe' => array('id_cle' => 1, 'm' => 'celsius', 's' => 'fahrenheit')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('avewind', ''), 'suffixe' => array('id_cle' => 1, 'm' => 'kph', 's' => 'mph')),
		'angle_vent'           => array('cle' => array('avewind', 'degrees')),
		'direction_vent'       => array('cle' => array()),
		// Données atmosphériques
		'risque_precipitation' => array('cle' => array()),
		'precipitation'        => array('cle' => array('qpf_allday', ''), 'suffixe' => array('id_cle' => 1, 'm' => 'mm', 's' => 'in')),
		'humidite'             => array('cle' => array('avehumidity')),
		'point_rosee'          => array('cle' => array()),
		'pression'             => array('cle' => array()),
		'visibilite'           => array('cle' => array()),
		'indice_uv'            => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('icon')),
		'icon_meteo'           => array('cle' => array('icon_url')),
		'desc_meteo'           => array('cle' => array('conditions')),
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
 * Construit le nom du cache en fonction du lieu, du type de données et de la langue utilisée par le site.
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
function wunderground_service2cache($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	$code_langue = ($configuration['condition'] == 'wunderground')
		? langue2code_wunderground($GLOBALS['spip_lang'])
		: $configuration['langue_service'];

	// Construction du chemin du fichier cache
	include_spip('inc/rainette_normaliser');
	$fichier_cache = normaliser_cache('wunderground', $lieu, $mode, $periodicite, $code_langue);

	return $fichier_cache;
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
	$code_langue = ($configuration['condition'] == 'wunderground')
		? langue2code_wunderground($GLOBALS['spip_lang'])
		: $configuration['langue_service'];

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
		if ($configuration['condition'] == 'wunderground') {
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

/**
 * @param $langue
 *
 * @return string
 */
function langue2code_wunderground($langue) {
	static $langue2wunderground = array(
		'aa'           => array('', ''),     // afar
		'ab'           => array('', ''),     // abkhaze
		'af'           => array('AF', ''),   // afrikaans
		'am'           => array('', ''),     // amharique
		'an'           => array('', 'SP'),   // aragonais
		'ar'           => array('AR', ''),   // arabe
		'as'           => array('', ''),     // assamais
		'ast'          => array('', 'SP'),   // asturien - iso 639-2
		'ay'           => array('', ''),     // aymara
		'az'           => array('AZ', ''),   // azeri
		'ba'           => array('', ''),     // bashkir
		'be'           => array('BY', ''),   // bielorusse
		'ber_tam'      => array('', ''),     // berbère
		'ber_tam_tfng' => array('', ''),     // berbère tifinagh
		'bg'           => array('BU', ''),   // bulgare
		'bh'           => array('', ''),     // langues biharis
		'bi'           => array('', ''),     // bichlamar
		'bm'           => array('', ''),     // bambara
		'bn'           => array('', ''),     // bengali
		'bo'           => array('', ''),     // tibétain
		'br'           => array('', 'FR'),   // breton
		'bs'           => array('', ''),     // bosniaque
		'ca'           => array('CA', ''),   // catalan
		'co'           => array('', 'FR'),   // corse
		'cpf'          => array('', 'FR'),   // créole réunionais
		'cpf_dom'      => array('', 'FR'),   // créole ???
		'cpf_hat'      => array('HT', ''),   // créole haïtien
		'cs'           => array('CZ', ''),   // tchèque
		'cy'           => array('CY', ''),   // gallois
		'da'           => array('DK', ''),   // danois
		'de'           => array('DL', ''),   // allemand
		'dz'           => array('', ''),     // dzongkha
		'el'           => array('GR', ''),   // grec moderne
		'en'           => array('EN', ''),   // anglais
		'en_hx'        => array('', 'EN'),   // anglais hacker
		'en_sm'        => array('', 'EN'),   // anglais smurf
		'eo'           => array('EO', ''),   // esperanto
		'es'           => array('SP', ''),   // espagnol
		'es_co'        => array('', 'SP'),   // espagnol colombien
		'es_mx_pop'    => array('', 'SP'),   // espagnol mexicain
		'et'           => array('ET', ''),   // estonien
		'eu'           => array('EU', ''),   // basque
		'fa'           => array('FA', ''),   // persan (farsi)
		'ff'           => array('', ''),     // peul
		'fi'           => array('FI', ''),   // finnois
		'fj'           => array('', 'EN'),   // fidjien
		'fo'           => array('', 'DK'),   // féroïen
		'fon'          => array('', ''),     // fon
		'fr'           => array('FR', ''),   // français
		'fr_sc'        => array('', 'FR'),   // français schtroumpf
		'fr_lpc'       => array('', 'FR'),   // français langue parlée
		'fr_lsf'       => array('', 'FR'),   // français langue des signes
		'fr_spl'       => array('', 'FR'),   // français simplifié
		'fr_tu'        => array('', 'FR'),   // français copain
		'fy'           => array('', 'DL'),   // frison occidental
		'ga'           => array('IR', ''),   // irlandais
		'gd'           => array('', 'EN'),   // gaélique écossais
		'gl'           => array('GZ', ''),   // galicien
		'gn'           => array('', ''),     // guarani
		'grc'          => array('', 'GR'),   // grec ancien
		'gu'           => array('GU', ''),   // goudjrati
		'ha'           => array('', ''),     // haoussa
		'hac'          => array('', 'KU'),   // Kurdish-Horami
		'hbo'          => array('', 'IL'),   // hebreu classique ou biblique
		'he'           => array('IL', ''),   // hébreu
		'hi'           => array('HI', ''),   // hindi
		'hr'           => array('CR', ''),   // croate
		'hu'           => array('HU', ''),   // hongrois
		'hy'           => array('HY', ''),   // armenien
		'ia'           => array('', ''),     // interlingua (langue auxiliaire internationale)
		'id'           => array('ID', ''),   // indonésien
		'ie'           => array('', ''),     // interlingue
		'ik'           => array('', ''),     // inupiaq
		'is'           => array('IS', ''),   // islandais
		'it'           => array('IT', ''),   // italien
		'it_fem'       => array('', 'IT'),   // italien féminin
		'iu'           => array('', ''),     // inuktitut
		'ja'           => array('JP', ''),   // japonais
		'jv'           => array('JW', ''),   // javanais
		'ka'           => array('KA', ''),   // géorgien
		'kk'           => array('', ''),     // kazakh
		'kl'           => array('', 'DK'),   // groenlandais
		'km'           => array('KM', ''),   // khmer central
		'kn'           => array('', ''),     // Kannada
		'ko'           => array('KR', ''),   // coréen
		'ks'           => array('', ''),     // kashmiri
		'ku'           => array('KU', ''),   // kurde
		'ky'           => array('', ''),     // kirghiz
		'la'           => array('LA', ''),   // latin
		'lb'           => array('', 'FR'),   // luxembourgeois
		'ln'           => array('', ''),     // lingala
		'lo'           => array('', ''),     // lao
		'lt'           => array('LT', ''),   // lituanien
		'lu'           => array('', ''),     // luba-katanga
		'lv'           => array('LV', ''),   // letton
		'man'          => array('GM', ''),   // mandingue
		'mfv'          => array('', ''),     // manjaque - iso-639-3
		'mg'           => array('', ''),     // malgache
		'mi'           => array('MI', ''),   // maori
		'mk'           => array('MK', ''),   // macédonien
		'ml'           => array('', ''),     // malayalam
		'mn'           => array('MN', ''),   // mongol
		'mo'           => array('', 'RO'),   // moldave ??? normalement c'est ro comme le roumain
		'mos'          => array('', ''),     // moré - iso 639-2
		'mr'           => array('MR', ''),   // marathe
		'ms'           => array('', ''),     // malais
		'mt'           => array('MT', ''),   // maltais
		'my'           => array('MY', ''),   // birman
		'na'           => array('', ''),     // nauruan
		'nap'          => array('', 'IT'),   // napolitain - iso 639-2
		'ne'           => array('', ''),     // népalais
		'nqo'          => array('', ''),     // n’ko - iso 639-3
		'nl'           => array('NL', ''),   // néerlandais
		'no'           => array('NO', ''),   // norvégien
		'nb'           => array('', 'NO'),   // norvégien bokmål
		'nn'           => array('', 'NO'),   // norvégien nynorsk
		'oc'           => array('OC', ''),   // occitan
		'oc_lnc'       => array('', 'OC'),   // occitan languedocien
		'oc_ni'        => array('', 'OC'),   // occitan niçard
		'oc_ni_la'     => array('', 'OC'),   // occitan niçard
		'oc_prv'       => array('', 'OC'),   // occitan provençal
		'oc_gsc'       => array('', 'OC'),   // occitan gascon
		'oc_lms'       => array('', 'OC'),   // occitan limousin
		'oc_auv'       => array('', 'OC'),   // occitan auvergnat
		'oc_va'        => array('', 'OC'),   // occitan vivaro-alpin
		'om'           => array('', ''),     // galla
		'or'           => array('', ''),     // oriya
		'pa'           => array('PA', ''),   // pendjabi
		'pbb'          => array('', ''),     // Nasa Yuwe (páez) - iso 639-3
		'pl'           => array('PL', ''),   // polonais
		'ps'           => array('PS', ''),   // pachto
		'pt'           => array('BR', ''),   // portugais
		'pt_br'        => array('', 'BR'),   // portugais brésilien
		'qu'           => array('', ''),     // quechua
		'rm'           => array('', ''),     // romanche
		'rn'           => array('', ''),     // rundi
		'ro'           => array('RO', ''),   // roumain
		'roa'          => array('chti', ''), // langues romanes (ch'ti) - iso 639-2
		'ru'           => array('RU', ''),   // russe
		'rw'           => array('', ''),     // rwanda
		'sa'           => array('', ''),     // sanskrit
		'sc'           => array('', 'IT'),   // sarde
		'scn'          => array('', 'IT'),   // sicilien - iso 639-2
		'sd'           => array('', ''),     // sindhi
		'sg'           => array('', ''),     // sango
		'sh'           => array('', 'SR'),   // serbo-croate
		'sh_latn'      => array('', 'SR'),   // serbo-croate latin
		'sh_cyrl'      => array('', 'SR'),   // serbo-croate cyrillique
		'si'           => array('', ''),     // singhalais
		'sk'           => array('SK', ''),   // slovaque
		'sl'           => array('SL', ''),   // slovène
		'sm'           => array('', ''),     // samoan
		'sn'           => array('', ''),     // shona
		'so'           => array('', ''),     // somali
		'sq'           => array('AL', ''),   // albanais
		'sr'           => array('SR', ''),   // serbe
		'src'          => array('', 'IT'),   // sarde logoudorien - iso 639-3
		'sro'          => array('', 'IT'),   // sarde campidanien - iso 639-3
		'ss'           => array('', ''),     // swati
		'st'           => array('', ''),     // sotho du Sud
		'su'           => array('', ''),     // soundanais
		'sv'           => array('SW', ''),   // suédois
		'sw'           => array('SI', ''),   // swahili
		'ta'           => array('', ''),     // tamoul
		'te'           => array('', ''),     // télougou
		'tg'           => array('', ''),     // tadjik
		'th'           => array('TH', ''),   // thaï
		'ti'           => array('', ''),     // tigrigna
		'tk'           => array('TK', ''),   // turkmène
		'tl'           => array('TL', ''),   // tagalog
		'tn'           => array('', ''),     // tswana
		'to'           => array('', ''),     // tongan (Îles Tonga)
		'tr'           => array('TR', ''),   // turc
		'ts'           => array('', ''),     // tsonga
		'tt'           => array('TT', ''),   // tatar
		'tw'           => array('', ''),     // twi
		'ty'           => array('', 'FR'),   // tahitien
		'ug'           => array('', ''),     // ouïgour
		'uk'           => array('UA', ''),   // ukrainien
		'ur'           => array('', ''),     // ourdou
		'uz'           => array('UZ', ''),   // ouszbek
		'vi'           => array('VU', ''),   // vietnamien
		'vo'           => array('', ''),     // volapük
		'wa'           => array('', 'FR'),   // wallon
		'wo'           => array('SN', ''),   // wolof
		'xh'           => array('', ''),     // xhosa
		'yi'           => array('YI', ''),   // yiddish
		'yo'           => array('', ''),     // yoruba
		'za'           => array('', 'CN'),   // zhuang
		'zh'           => array('CN', ''),   // chinois (ecriture simplifiee)
		'zh_tw'        => array('TW', ''),   // chinois taiwan (ecriture traditionnelle)
		'zu'           => array('', '')      // zoulou
	);

	$code = $GLOBALS['rainette_wunderground_config']['service']['langue_service'];
	if (array_key_exists($langue, $langue2wunderground)) {
		if ($c0 = $langue2wunderground[$langue][0]) {
			$code = strtoupper($c0);
		} elseif ($c1 = $langue2wunderground[$langue][1]) {
			$code = strtoupper($c1);
		}
	}

	return $code;
}
