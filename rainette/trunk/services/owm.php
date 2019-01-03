<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service Open Weather Map (owm).
 * Ce service fournit des données au format XML ou JSON mais Rainette utilise uniquement le JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\OWM
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_OWM_URL_BASE_REQUETE')) {
	/**
	 * URL de base (endpoint) des requêtes au service OpenWeatherMap.
	 */
	define('_RAINETTE_OWM_URL_BASE_REQUETE', 'http://api.openweathermap.org/data/2.5/');
}
if (!defined('_RAINETTE_OWM_URL_BASE_ICONE')) {
	/**
	 * URL de base des icônes fournis par le service OpenWeatherMap.
	 */
	define('_RAINETTE_OWM_URL_BASE_ICONE', 'http://openweathermap.org/img/w');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_owm_config']['service'] = array(
	'alias'          => 'owm',
	'nom'            => 'OpenWeatherMap',
	'credits'        => array(
		'titre' => null,
		'logo'  => null,
		'lien'  => 'http://openweathermap.org/',
	),
	'termes'         => array(
		'titre' => 'Terms of service',
		'lien'  => 'http://openweathermap.org/terms'
	),
	'enregistrement' => array(
		'titre'      => 'Members',
		'lien'       => 'https://home.openweathermap.org/users/sign_up',
		'taille_cle' => 32
	),
	'offres'         => array(
		'titre'   => 'Price',
		'lien'    => 'https://openweathermap.org/price',
		'limites' => array(
			'minute' => 60
		),
	),
	'langues'        => array(
		'disponibles' => array(
			'bg'    => 'bg',
			'ca'    => 'ca',
			'de'    => 'de',
			'en'    => 'en',
			'es'    => 'es',
			'fi'    => 'fi',
			'fr'    => 'fr',
			'hr'    => 'hr',
			'it'    => 'it',
			'nl'    => 'nl',
			'pl'    => 'pl',
			'pt'    => 'pt',
			'ro'    => 'ro',
			'ru'    => 'ru',
			'sv'    => 'sv',
			'tr'    => 'tr',
			'uk'    => 'uk',
			'zh'    => 'zh',
			'zh_tw' => 'zh_tw',
		),
		'defaut'      => 'en'
	),
	'defauts'        => array(
		'inscription'   => '',
		'unite'         => 'm',
		'condition'     => 'owm',
		'theme'         => '',
		'theme_local'   => '',
		'theme_weather' => 'sticker',
	),
	// TODO : a revoir, liste des codes owm ok
	'transcodage_weather' => array(
		'200' => array(41, 46),
		'201' => array(39, 45),
		'202' => array(39, 45),
		'210' => array(41, 46),
		'211' => array(38, 47),
		'212' => array(32, 31),
		'221' => array(26, 26),
		'230' => array(15, 15),
		'231' => array(20, 20),
		'232' => array(21, 21),
		'300' => array(28, 27),
		'301' => array(34, 33),
		'302' => array(30, 29),
		'310' => array(28, 27),
		'311' => array(5, 5),
		'312' => array(11, 11),
		'313' => array(16, 16),
		'314' => array(32, 31),
		'321' => array(4, 4),
		'500' => array(4, 4),
		'501' => array(4, 4),
		'502' => array(30, 29),
		'503' => array(26, 26),
		'504' => array(26, 26),
		'511' => array(26, 26),
		'520' => array(26, 26),
		'521' => array(26, 26),
		'522' => array(26, 26),
		'531' => array(26, 26),
		'600' => array(26, 26),
		'601' => array(26, 26),
		'602' => array(26, 26),
		'611' => array(26, 26),
		'612' => array(26, 26),
		'615' => array(26, 26),
		'616' => array(26, 26),
		'620' => array(26, 26),
		'621' => array(26, 26),
		'622' => array(26, 26),
		'701' => array(26, 26),
		'711' => array(26, 26),
		'721' => array(26, 26),
		'731' => array(26, 26),
		'741' => array(26, 26),
		'751' => array(26, 26),
		'761' => array(26, 26),
		'762' => array(26, 26),
		'771' => array(26, 26),
		'781' => array(26, 26),
		'800' => array(26, 26),
		'801' => array(26, 26),
		'802' => array(26, 26),
		'803' => array(26, 26),
		'804' => array(26, 26),
		'900' => array(0, 0),
		'901' => array(26, 26),
		'902' => array(26, 26),
		'903' => array(26, 26),
		'904' => array(26, 26),
		'905' => array(26, 26),
		'906' => array(26, 26),
		'951' => array(26, 26),
		'952' => array(26, 26),
		'953' => array(26, 26),
		'954' => array(26, 26),
		'955' => array(26, 26),
		'956' => array(26, 26),
		'957' => array(26, 26),
		'958' => array(26, 26),
		'959' => array(26, 26),
		'960' => array(26, 26),
		'961' => array(26, 26),
		'962' => array(26, 26)
	)
);

// Configuration des données fournies par le service owm pour le mode 'infos'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_owm_config']['infos'] = array(
	'periode_maj' => 3600 * 24 * 30,
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
$GLOBALS['rainette_owm_config']['conditions'] = array(
	'periode_maj' => 3600 * 2,
	'format_flux' => 'json',
	'cle_base'    => array(),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('dt')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('main', 'temp')),
		'temperature_ressentie' => array('cle' => array()),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('wind', 'speed')),
		'angle_vent'            => array('cle' => array('wind', 'deg')),
		'direction_vent'        => array('cle' => array()),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array()),
		'humidite'              => array('cle' => array('main', 'humidity')),
		'point_rosee'           => array('cle' => array()),
		'pression'              => array('cle' => array('main', 'pressure')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('visibility')),
		'indice_uv'             => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('weather', 0, 'id')),
		'icon_meteo'            => array('cle' => array('weather', 0, 'icon')),
		'desc_meteo'            => array('cle' => array('weather', 0, 'description')),
		'trad_meteo'            => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service owm pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_owm_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 16),
		//		3                      => array('max_jours' => 5)
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 3600 * 2,
	'format_flux'        => 'json',
	'cle_base'           => array('list'),
	'cle_heure'          => array(),
	'structure_heure'    => false,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('dt')),
		'heure'                => array('cle' => array()),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array()),
		'coucher_soleil'       => array('cle' => array()),
		// Températures
		'temperature'          => array('cle' => array()),
		'temperature_max'      => array('cle' => array('temp', 'max')),
		'temperature_min'      => array('cle' => array('temp', 'min')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('speed')),
		'angle_vent'           => array('cle' => array('deg')),
		'direction_vent'       => array('cle' => array()),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array()),
		'precipitation'        => array('cle' => array('rain')),
		'humidite'             => array('cle' => array('humidity')),
		'point_rosee'          => array('cle' => array()),
		'pression'             => array('cle' => array('pressure')),
		'visibilite'           => array('cle' => array()),
		'indice_uv'            => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('weather', 0, 'id')),
		'icon_meteo'           => array('cle' => array('weather', 0, 'icon')),
		'desc_meteo'           => array('cle' => array('weather', 0, 'description')),
		'trad_meteo'           => array('cle' => array()),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service owm en cas d'erreur.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_owm_config']['erreurs'] = array(
	'cle_base' => array(),
	'donnees'  => array(
		// Erreur
		'code'    => array('cle' => array('cod')),
		'message' => array('cle' => array('message')),
	),
);


/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
 * unique de chargement des données météorologiques `meteo_charger()`.
 * PACKAGE SPIP\RAINETTE\OWM\API
 * ------------------------------------------------------------------------------------------------
 *
 * @param mixed $mode
 */

/**
 * @param string $mode
 *
 * @return array
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
	$code_langue = langue_determiner($configuration);

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays et le format latitude,longitude
	$lieu_normalise = lieu_normaliser($lieu, $format_lieu);
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
 * @param array $erreur
 *
 * @return bool
 */
function owm_erreur_verifier($erreur) {

	// Initialisation
	$est_erreur = false;

	// Pour OWM une erreur possède deux attributs, le code et le message.
	// Néanmoins, le code 200 est aussi renvoyé pour dire ok sans message.
	// => il faut donc écarter ce cas d'une erreur.
	if (!empty($erreur['code']) and !empty($erreur['message']) and ($erreur['code'] != '200')) {
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
function owm_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Calcul de la température ressentie et de la direction du vent (16 points), celles-ci
		// n'étant pas fournie nativement par owm
		include_spip('inc/rainette_convertir');
		$tableau['temperature_ressentie'] = temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']);
		$tableau['direction_vent'] = angle2direction($tableau['angle_vent']);
		// On convertit aussi la visibilité en km car elle est fournie en mètres.
		$tableau['visibilite'] = metre2kilometre($tableau['visibilite']);

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
		// Vérifier les précipitations. Pour les prévisions, OWM renvoie le champ rain uniquement si il est
		// différent de zéro. Il faut donc rétablir la valeur zéro dans ce cas pour éviter d'avoir N/D lors de
		// l'affichage.
		if ($tableau['precipitation'] === '') {
			$tableau['precipitation'] = 0;
		}

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_owm($tableau, $configuration);
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
function etat2resume_owm(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service le nom du fichier icone finit par "d" pour le jour et
		// par "n" pour la nuit.
		if (strpos($tableau['icon_meteo'], 'n') === false) {
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
			$url = _RAINETTE_OWM_URL_BASE_ICONE . '/' . $tableau['icon_meteo'] . '.png';
			$tableau['icone']['source'] = copie_locale($url);
		} else {
			include_spip('inc/rainette_normaliser');
			if ($configuration['condition'] == "{$configuration['alias']}_local") {
				// On affiche un icône d'un thème local compatible avec OWM.
				// TODO : à vérifier car aucun thème n'est pour l'instant disponible
				$chemin = icone_local_normaliser(
					"{$tableau['icon_meteo']}.png",
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
