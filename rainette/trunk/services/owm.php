<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service Open Weather Map (owm).
 * Ce service fournit des données au format XML ou JSON.
 *
 * @package SPIP\RAINETTE\OWM
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_OWM_URL_BASE_REQUETE'))
	define('_RAINETTE_OWM_URL_BASE_REQUETE', 'http://api.openweathermap.org/data/2.5/');
if (!defined('_RAINETTE_OWM_URL_BASE_ICONE'))
	define('_RAINETTE_OWM_URL_BASE_ICONE', 'http://openweathermap.org/img/w');
if (!defined('_RAINETTE_OWM_JOURS_PREVISIONS'))
	define('_RAINETTE_OWM_JOURS_PREVISIONS', 14);
if (!defined('_RAINETTE_OWM_LANGUE_DEFAUT'))
	define('_RAINETTE_OWM_LANGUE_DEFAUT', 'FR');


/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
 * unique de chargement des données météorologiques `charger_meteo()`.
 * PACKAGE SPIP\RAINETTE\OWM\API
 * ------------------------------------------------------------------------------------------------
 */


/**
 * @param $lieu
 * @param $mode
 * @return string
 */
function owm_service2cache($lieu, $mode) {
	include_spip('inc/config');
	$condition = lire_config('rainette/owm/condition');
	$langue = $GLOBALS['spip_lang'];

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'owm');
	$fichier_cache = $dir . str_replace(array(',', '+', '.', '/'), '-', $lieu) 
				   . "_" . $mode 
				   . ((($condition == 'owm') AND ($mode != 'infos')) ? '-' . $langue : '')
				   . ".txt";

	return $fichier_cache;
}

/**
 * @param $lieu
 * @param $mode
 * @return string
 */
function owm_service2url($lieu, $mode) {
	include_spip('inc/config');

	// Determination de la demande
	$demande = ($mode == 'previsions') ? 'forecast/daily' : 'weather';

	// Determination du format d'échange
	$format = lire_config('rainette/owm/format', 'xml');

	// Identification du système d'unité
	$unite = lire_config('rainette/owm/unite', 'm');

	// Clé d'inscription facultative
	$cle = lire_config('rainette/owm/inscription');

	// Identification de la langue du resume.
	// Le choix de la langue n'a d'interet que si on utilise le resume natif du service. Si ce n'est pas le cas
	// on ne la precise pas et on laisse l'API renvoyer la langue par defaut
	$condition = lire_config('rainette/owm/condition', 'owm');
	$code_langue = '';
	if ($condition == 'owm')
		$code_langue = langue2code_owm($GLOBALS['spip_lang']);

	$url = _RAINETTE_OWM_URL_BASE_REQUETE
		.  $demande. '?'
		.  'q=' . trim($lieu)
		.  '&mode=' . $format
		.  '&units=' . ($unite == 'm' ? 'metric' : 'imperial')
		.  ($mode == 'previsions' ? '&cnt=' . _RAINETTE_OWM_JOURS_PREVISIONS : '')
		.  ($code_langue ? '&lang=' . $code_langue : '')
		.  ($cle ? '&APPID=' . $cle : '');

	return $url;
}


/**
 * Renvoie le système d'unité utilisé pour acquérir les données du service
 *
 * @return string
 */
function owm_service2unite() {
	include_spip('inc/config');

	// Identification du système d'unité
	$unite = lire_config('rainette/owm/unite', 'm');

	return $unite;
}


/**
 * @param $mode
 * @return int
 */
function owm_service2reload_time($mode) {
	static $reload = array('conditions' => 7200, 'previsions' => 10800);

	return $reload[$mode];
}

/**
 * @param $url
 * @return array
 */
function owm_url2flux($url) {
	// Déterminer le format d'échange pour aiguiller vers la bonne conversion
	include_spip('inc/config');
	$format = lire_config('rainette/owm/format', 'xml');

	include_spip('inc/phraser');
	$flux = ($format == 'xml') ? url2flux_xml($url, false) : url2flux_json($url);

	return $flux;
}


/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * ne gere pas encore le jour et la nuit de la date courante suivant l'heure!!!!
 * @param array $flux
 * @return array
 */
function owm_flux2previsions($flux, $lieu) {
	// Identifier le format d'échange des données
	include_spip('inc/config');
	$format = lire_config('rainette/owm/format', 'xml');

	// Identification des suffixes d'unite pour choisir le bon champ
	// -> wunderground fournit toujours les valeurs dans les deux systemes d'unites
	$unite = lire_config('rainette/owm/unite', 'm');

	// Construire le tableau standard des prévisions météorologiques propres au service
	$tableau = ($format == 'xml') ? xml2previsions_owm($flux, $unite) : json2previsions_owm($flux, $unite);

	// Compléter le tableau standard avec les états météorologiques calculés pour chaque jour
	if ($tableau) {
		$condition = lire_config('rainette/owm/condition', 'owm');
		foreach ($tableau as $_index => $_prevision) {
			if ($_prevision[0]['code_meteo']
			AND $_prevision[0]['icon_meteo']
			AND isset($_prevision[0]['desc_meteo'])) {
				// Le mode jour/nuit n'est pas supporté par ce service.
				$tableau[$_index]['periode'] = 0; // jour

				// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
				if ($condition == 'owm') {
					// On affiche les prévisions natives fournies par le service.
				// Celles-ci etant deja traduites dans la bonne langue on stocke le texte exact retourne par l'API
					$tableau[$_index][0]['icone']['code'] = $_prevision[0]['code_meteo'];
					$url = _RAINETTE_OWM_URL_BASE_ICONE . '/' . $_prevision[0]['icon_meteo'] . '.png';
					$tableau[$_index][0]['icone']['url'] = copie_locale($url);
					$tableau[$_index][0]['resume'] = ucfirst($_prevision[0]['desc_meteo']);
				}
				else {
					// On affiche les conditions traduites dans le systeme weather.com
					$meteo = meteo_owm2weather($_prevision[0]['code_meteo'], $tableau[$_index]['periode']);
					$tableau[$_index][0]['icone'] = $meteo;
					$tableau[$_index][0]['resume'] = $meteo;
				}
			}
		}
	}

	// Traitement des erreurs de flux
	$index = count($tableau);
	$tableau[$index]['erreur'] = (!$tableau) ? 'chargement' : '';

	// Ajout des informations communes dans l'index adéquat
	$tableau[$index]['max_jours'] = _RAINETTE_OWM_JOURS_PREVISIONS;

	return $tableau;
}

/**
 * @param $flux
 * @param $lieu
 * @return array
 */
function owm_flux2conditions($flux, $lieu) {
	// Identifier le format d'échange des données
	include_spip('inc/config');
	$format = lire_config('rainette/owm/format', 'xml');

	// Construire le tableau standard des conditions météorologiques propres au service
	$tableau = ($format == 'xml') ? xml2conditions_owm($flux) : json2conditions_owm($flux);

	// Compléter le tableau standard avec les états météorologiques calculés
	if ($tableau['code_meteo']
	AND $tableau['icon_meteo']
	AND isset($tableau['desc_meteo'])) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service le nom du fichier icone finit par "d" pour le jour et
		// par "n" pour la nuit.
		$icone = $tableau['icon_meteo'];
		if (strpos($icone, 'n') === false)
			$tableau['periode'] = 0; // jour
		else
			$tableau['periode'] = 1; // nuit

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		$condition = lire_config('rainette/owm/condition', 'owm');
		if ($condition == 'owm') {
			// On affiche les conditions natives fournies par le service.
			// Celles-ci etant deja traduites dans la bonne langue on stocke le texte exact retourne par l'API
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$url = _RAINETTE_OWM_URL_BASE_ICONE . '/' . $tableau['icon_meteo'] . '.png';
			$tableau['icone']['url'] = copie_locale($url);
			$tableau['resume'] = ucfirst($tableau['desc_meteo']);
		}
		else {
			// On affiche les conditions traduites dans le systeme weather.com
			// Pour le resume on stocke le code et non la traduction pour eviter de generer
			// un cache par langue comme pour le mode natif. La traduction est faite via les fichiers de langue
			$meteo = meteo_owm2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? 'chargement' : '';

	return $tableau;
}

/**
 * @param $flux
 * @param $lieu
 * @return array
 */
function owm_flux2infos($flux, $lieu) {
	// Identifier le format d'échange des données
	include_spip('inc/config');
	$format = lire_config('rainette/owm/format', 'xml');

	// Construire le tableau standard des informations sur le lieu
	$tableau = ($format == 'xml') ? xml2infos_owm($flux) : json2infos_owm($flux);

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? 'chargement' : '';

	return $tableau;
}

/**
 * @return array
 */
function owm_service2credits() {
	$credits = array('titre' => '', 'logo' => '');
	$credits['lien'] = 'http://openweathermap.org/';

	return $credits;
}


/**
 * -----------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont permettent le décodage des données météorologiques reçues au
 * format XML. Ce sont des sous-fonctions internes appelées uniquement par les fonctions de l'API.
 * PACKAGE SPIP\RAINETTE\OWM\XML
 * -----------------------------------------------------------------------------------------------
 */


function xml2previsions_owm($flux, $unite) {
	$tableau = array();

	if (isset($flux['children']['forecast'][0]['children']['time'])) {
		$previsions = $flux['children']['forecast'][0]['children']['time'];
		$maintenant = time();

		if ($previsions) {
			include_spip('inc/convertir');
			foreach ($previsions as $_index => $_prevision) {
				if (isset($_prevision['children'])) {
					// Index du jour et date du jour
					$tableau[$_index]['index'] = $_index;
					$tableau[$_index]['date'] = (isset($_prevision['attributes']['day']))
						? $_prevision['attributes']['day']
						: date('Y-m-d', $maintenant + 24*3600*$_index);

					$_prevision = $_prevision['children'];
					// Date complete des lever/coucher du soleil.
					// OWM ne fournissant que ces données pour le jour courant, on ne les retient pas.
					$tableau[$_index]['lever_soleil'] = NULL;
					$tableau[$_index]['coucher_soleil'] = NULL;

					// Previsions du jour
					$tableau[$_index][0]['temperature_max'] = (isset($_prevision['temperature'][0]['attributes'])) ? floatval($_prevision['temperature'][0]['attributes']['max']) : '';
					$tableau[$_index][0]['temperature_min'] = (isset($_prevision['temperature'][0]['attributes'])) ? floatval($_prevision['temperature'][0]['attributes']['min']) : '';
					$tableau[$_index][0]['vitesse_vent'] = (isset($_prevision['windspeed'][0]['attributes'])) ? floatval($_prevision['windspeed'][0]['attributes']['mps']) : '';
					$tableau[$_index][0]['angle_vent'] = (isset($_prevision['winddirection'][0]['attributes'])) ? intval($_prevision['winddirection'][0]['attributes']['deg']) : '';
					$tableau[$_index][0]['direction_vent'] = (isset($_prevision['winddirection'][0]['attributes'])) ? $_prevision['winddirection'][0]['attributes']['code'] : '';

					$tableau[$_index][0]['risque_precipitation'] = NULL;
					// -- OWM utilise la balise precipitation pour le la pluie ou la neige. Il faut donc tester le type pour ne sélectionner
					//    que la pluie.
					$tableau[$_index][0]['precipitation'] =
						(isset($_prevision['precipitation'][0]['attributes']) AND ($_prevision['precipitation'][0]['attributes']['type'] == 'rain'))
							? floatval($_prevision['precipitation'][0]['attributes']['value'])
							: 0;
					if (($unite == 's') AND $tableau[$_index][0]['precipitation'])
						$tableau[$_index][0]['precipitation'] = millimetre2inch($tableau[$_index][0]['precipitation']);
					$tableau[$_index][0]['humidite'] = (isset($_prevision['humidity'][0]['attributes'])) ? intval($_prevision['humidity'][0]['attributes']['value']) : '';
					$tableau[$_index][0]['pression'] = (isset($_prevision['pressure'][0]['attributes'])) ? floatval($_prevision['pressure'][0]['attributes']['value']) : '';

					$tableau[$_index][0]['code_meteo'] = (isset($_prevision['symbol'][0]['attributes'])) ? intval($_prevision['symbol'][0]['attributes']['number']) : '';
					$tableau[$_index][0]['icon_meteo'] = (isset($_prevision['symbol'][0]['attributes'])) ? $_prevision['symbol'][0]['attributes']['var'] : '';
					$tableau[$_index][0]['desc_meteo'] = (isset($_prevision['symbol'][0]['attributes'])) ? $_prevision['symbol'][0]['attributes']['name'] : '';

					// Previsions de la nuit si elle existe
					$tableau[$_index][1] = NULL;
				}
			}
		}
	}

	return $tableau;
}


function xml2conditions_owm($flux) {
	$tableau = array();

	if (isset($flux['children'])) {
		include_spip('inc/convertir');
		$conditions = $flux['children'];

		// Date d'observation
		$date_maj = (isset($conditions['lastupdate'])) ? strtotime($conditions['lastupdate'][0]['attributes']['value']) : 0;
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		$tableau['station'] = NULL;

		// Liste des conditions meteo
		if ($conditions['wind'][0]['children']) {
			$conditions_vent = $conditions['wind'][0]['children'];

			$tableau['vitesse_vent'] = (isset($conditions_vent['speed'])) ? floatval($conditions_vent['speed'][0]['attributes']['value']) : '';
			$tableau['angle_vent'] = (isset($conditions_vent['direction'])) ? intval($conditions_vent['direction'][0]['attributes']['value']) : '';
			$tableau['direction_vent'] = (isset($conditions_vent['direction']))	? $conditions_vent['direction'][0]['attributes']['code'] : '';
		}

		$tableau['temperature_reelle'] = (isset($conditions['temperature'])) ? floatval($conditions['temperature'][0]['attributes']['value']) : '';
		$tableau['temperature_ressentie'] = (isset($conditions['temperature'])) ? temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']) : '';

		$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity'][0]['attributes']['value']) : '';
		$tableau['point_rosee'] = NULL;

		$tableau['pression'] = (isset($conditions['pressure'])) ? floatval($conditions['pressure'][0]['attributes']['value']) : '';
		$tableau['tendance_pression'] = NULL;

		$tableau['visibilite'] = NULL;

		// Code meteo, resume et icone natifs au service
		$tableau['code_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'][0]['attributes']['number'] : '';
		$tableau['icon_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'][0]['attributes']['icon'] : '';
		$tableau['desc_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'][0]['attributes']['value'] : '';
	}

	return $tableau;
}

function xml2infos_owm($flux) {
	$tableau = array();

	if (isset($flux['children']['city'][0]['attributes']['name'])) {
		$tableau['ville'] = $flux['children']['city'][0]['attributes']['name'];
	}

	if (isset($flux['children']['city'][0]['children']['coord'][0]['attributes'])) {
		$infos = $flux['children']['city'][0]['children']['coord'][0]['attributes'];

		$tableau['region'] = NULL;

		$tableau['longitude'] = (isset($infos['lon'])) ? floatval($infos['lon']) : '';
		$tableau['latitude'] = (isset($infos['lat'])) ? floatval($infos['lat']) : '';

		$tableau['population'] = NULL;
	}

	return $tableau;
}


/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont permettent le décodage des données météorologiques reçues au
 * format JSON. Ce sont des sous-fonctions internes appelées uniquement par les fonctions de l'API.
 * PACKAGE SPIP\RAINETTE\OWM\JSON
 * ------------------------------------------------------------------------------------------------
 */


function json2previsions_owm($flux, $unite) {
	$tableau = array();

	if (isset($flux['list'])) {
		$previsions = $flux['list'];
		$maintenant = time();

		if ($previsions) {
			include_spip('inc/convertir');
			foreach ($previsions as $_index => $_prevision) {
				// Index du jour et date du jour
				$tableau[$_index]['index'] = $_index;
				$tableau[$_index]['date'] = (isset($_prevision['dt']))
					? date('Y-m-d', $_prevision['dt'])
					: date('Y-m-d', $maintenant + 24*3600*$_index);

				// Date complete des lever/coucher du soleil.
				// OWM ne fournissant que ces données pour le jour courant, on ne les retient pas.
				$tableau[$_index]['lever_soleil'] = NULL;
				$tableau[$_index]['coucher_soleil'] = NULL;

				// Previsions du jour
				$tableau[$_index][0]['temperature_max'] = (isset($_prevision['temp'])) ? floatval($_prevision['temp']['max']) : '';
				$tableau[$_index][0]['temperature_min'] = (isset($_prevision['temp'])) ? floatval($_prevision['temp']['min']) : '';
				$tableau[$_index][0]['vitesse_vent'] = (isset($_prevision['speed'])) ? floatval($_prevision['speed']) : '';
				$tableau[$_index][0]['angle_vent'] = (isset($_prevision['deg'])) ? intval($_prevision['deg']) : '';
				// le flux JSON ne fournit pas la direction en 16 points
				$tableau[$_index][0]['direction_vent'] = (isset($_prevision['deg'])) ? angle2direction($tableau[$_index][0]['angle_vent']) : '';

				$tableau[$_index][0]['risque_precipitation'] = NULL;
				// le flux JSON ne fournit pas la précipitation en mm
				$tableau[$_index][0]['precipitation'] = NULL;
				$tableau[$_index][0]['humidite'] = (isset($_prevision['humidity'])) ? intval($_prevision['humidity']) : '';
				$tableau[$_index][0]['pression'] = (isset($_prevision['pressure'])) ? floatval($_prevision['pressure']) : '';

				$tableau[$_index][0]['code_meteo'] = (isset($_prevision['weather'][0])) ? intval($_prevision['weather'][0]['id']) : '';
				$tableau[$_index][0]['icon_meteo'] = (isset($_prevision['weather'][0])) ? $_prevision['weather'][0]['icon'] : '';
				$tableau[$_index][0]['desc_meteo'] = (isset($_prevision['weather'][0])) ? $_prevision['weather'][0]['description'] : '';

				// Previsions de la nuit si elle existe
				$tableau[$_index][1] = NULL;
			}
		}
	}

	return $tableau;
}


function json2conditions_owm($flux) {
	$tableau = array();

	if ($flux) {
		include_spip('inc/convertir');
		$conditions = $flux;

		// Date d'observation
		$date_maj = (isset($conditions['dt'])) ? intval($conditions['dt']) : 0;
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		$tableau['station'] = NULL;

		if ($flux['wind']) {
			$conditions = $flux['wind'];

			// Données anémométriques
			$tableau['vitesse_vent'] = (isset($conditions['speed'])) ? floatval($conditions['speed']) : '';
			$tableau['angle_vent'] = (isset($conditions['deg'])) ? intval($conditions['deg']) : '';
			// Contrairement au flux XML le flux JSON ne fournit pas la direction abrégée en anglais
			// --> on la calcule
			$tableau['direction_vent'] = (isset($conditions['deg'])) ? angle2direction($tableau['angle_vent']) : '';
		}

		if (isset($flux['main'])) {
			$conditions = $flux['main'];

			// Températures et données atmosphériques
			$tableau['temperature_reelle'] = (isset($conditions['temp'])) ? floatval($conditions['temp']) : '';
			$tableau['temperature_ressentie'] = (isset($conditions['temp'])) ? temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']) : '';

			$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity']) : '';
			$tableau['point_rosee'] = NULL;

			$tableau['pression'] = (isset($conditions['pressure'])) ? floatval($conditions['pressure']) : '';
			$tableau['tendance_pression'] = NULL;

			$tableau['visibilite'] = NULL;
		}

		if (isset($flux['weather'][0])) {
			$conditions = $flux['weather'][0];

			// Code meteo, resume et icone natifs au service
			$tableau['code_meteo'] = (isset($conditions['id'])) ? $conditions['id'] : '';
			$tableau['icon_meteo'] = (isset($conditions['icon'])) ? $conditions['icon'] : '';
			$tableau['desc_meteo'] = (isset($conditions['description'])) ? $conditions['description'] : '';
		}
	}

	return $tableau;
}

function json2infos_owm($flux) {
	$tableau = array();

	if (isset($flux['name'])) {
		$tableau['ville'] = $flux['name'];
	}

	if (isset($flux['coord'])) {
		$infos = $flux['coord'];

		$tableau['region'] = NULL;

		$tableau['longitude'] = (isset($infos['lon'])) ? floatval($infos['lon']) : '';
		$tableau['latitude'] = (isset($infos['lat'])) ? floatval($infos['lat']) : '';

		$tableau['population'] = NULL;
	}

	return $tableau;
}



/**
 * ---------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont des utilitaires utilisés uniquement appelées par les fonctions
 * de l'API.
 * PACKAGE SPIP\RAINETTE\OWM\OUTILS
 * ---------------------------------------------------------------------------------------------
 */

// TODO : mettre au point le transcodage omw vers weather
function meteo_owm2weather($meteo, $periode=0) {
	static $owm2weather = array(
							'chanceflurries'=> array(41,46),
							'chancerain'=> array(39,45),
							'chancesleet'=> array(39,45),
							'chancesleet'=> array(41,46),
							'chancesnow'=> array(41,46),
							'chancetstorms'=> array(38,47),
							'clear'=> array(32,31),
							'cloudy'=> array(26,26),
							'flurries'=> array(15,15),
							'fog'=> array(20,20),
							'hazy'=> array(21,21),
							'mostlycloudy'=> array(28,27),
							'mostlysunny'=> array(34,33),
							'partlycloudy'=> array(30,29),
							'partlysunny'=> array(28,27),
							'sleet'=> array(5,5),
							'rain'=> array(11,11),
							'sleet'=> array(5,5),
							'snow'=> array(16,16),
							'sunny'=> array(32,31),
							'tstorms'=> array(4,4),
							'thunderstorms'=> array(4,4),
							'unknown'=> array(4,4),
							'cloudy'=> array(26,26),
							'scatteredclouds'=> array(30,29),
							'overcast'=> array(26,26));

	$icone = 'na';
	if (array_key_exists($meteo,  $owm2weather))
		$icone = strval($owm2weather[$meteo][$periode]);
	return $icone;
}

/**
 * @param $langue
 * @return string
 */
function langue2code_owm($langue) {
	static $langue2owm = array(
		'aa' => array('', ''), 					// afar
		'ab' => array('', ''), 					// abkhaze
		'af' => array('', ''), 				// afrikaans
		'am' => array('', ''), 					// amharique
		'an' => array('', 'sp'),				// aragonais
		'ar' => array('', ''), 				// arabe
		'as' => array('', ''), 					// assamais
		'ast' => array('', 'sp'), 				// asturien - iso 639-2
		'ay' => array('', ''), 					// aymara
		'az' => array('', 'ru'), 				// azeri
		'ba' => array('', ''),					// bashkir
		'be' => array('', 'ru'), 				// bielorusse
		'ber_tam' => array('', ''),				// berbère
		'ber_tam_tfng' => array('', ''),		// berbère tifinagh
		'bg' => array('bg', ''), 				// bulgare
		'bh' => array('', ''),					// langues biharis
		'bi' => array('', ''),					// bichlamar
		'bm' => array('', ''),					// bambara
		'bn' => array('', ''),					// bengali
		'bo' => array('', ''),					// tibétain
		'br' => array('', 'fr'),				// breton
		'bs' => array('', ''),					// bosniaque
		'ca' => array('', 'sp'),				// catalan
		'co' => array('', 'fr'),				// corse
		'cpf' => array('', 'fr'), 				// créole réunionais
		'cpf_dom' => array('', 'sp'), 			// créole ???
		'cpf_hat' => array('', 'fr'), 			// créole haïtien
		'cs' => array('cz', ''),				// tchèque
		'cy' => array('', 'en'),				// gallois
		'da' => array('', ''),				// danois
		'de' => array('de', ''),				// allemand
		'dz' => array('', ''),					// dzongkha
		'el' => array('', ''),				// grec moderne
		'en' => array('en', ''),				// anglais
		'en_hx' => array('', 'en'),				// anglais hacker
		'en_sm' => array('', 'en'),				// anglais smurf
		'eo' => array('', ''),				// esperanto
		'es' => array('sp', ''),				// espagnol
		'es_co' => array('', 'sp'),				// espagnol colombien
		'es_mx_pop' => array('', 'sp'),			// espagnol mexicain
		'et' => array('', ''),				// estonien
		'eu' => array('', 'fr'),				// basque
		'fa' => array('', ''),				// persan (farsi)
		'ff' => array('', ''),					// peul
		'fi' => array('fi', ''),				// finnois
		'fj' => array('', 'en'),				// fidjien
		'fo' => array('', ''),				// féroïen
		'fon' => array('', ''),					// fon
		'fr' => array('fr', ''),				// français
		'fr_sc' => array('', 'fr'),				// français schtroumpf
		'fr_lpc' => array('', 'fr'),			// français langue parlée
		'fr_lsf' => array('', 'fr'),			// français langue des signes
		'fr_spl' => array('', 'fr'),			// français simplifié
		'fr_tu' => array('', 'fr'),				// français copain
		'fy' => array('', 'de'),				// frison occidental
		'ga' => array('', 'en'),				// irlandais
		'gd' => array('', 'en'),				// gaélique écossais
		'gl' => array('', 'sp'),				// galicien
		'gn' => array('', ''),					// guarani
		'grc' => array('', ''),				// grec ancien
		'gu' => array('', ''),				// goudjrati
		'ha' => array('', ''),					// haoussa
		'hac' => array('', ''), 				// Kurdish-Horami
		'hbo' => array('', ''),				// hebreu classique ou biblique
		'he' => array('', ''),				// hébreu
		'hi' => array('', ''),				// hindi
		'hr' => array('', ''),				// croate
		'hu' => array('', ''),	 			// hongrois
		'hy' => array('', ''), 				// armenien
		'ia' => array('', ''),					// interlingua (langue auxiliaire internationale)
		'id' => array('', ''),				// indonésien
		'ie' => array('', ''),					// interlingue
		'ik' => array('', ''),					// inupiaq
		'is' => array('', ''),				// islandais
		'it' => array('it', ''),				// italien
		'it_fem' => array('', 'it'),			// italien féminin
		'iu' => array('', ''),					// inuktitut
		'ja' => array('', ''),				// japonais
		'jv' => array('', ''),				// javanais
		'ka' => array('', ''),				// géorgien
		'kk' => array('', ''),					// kazakh
		'kl' => array('', ''),				// groenlandais
		'km' => array('', ''),				// khmer central
		'kn' => array('', ''),					// Kannada
		'ko' => array('', ''),				// coréen
		'ks' => array('', ''),					// kashmiri
		'ku' => array('', ''),				// kurde
		'ky' => array('', ''),					// kirghiz
		'la' => array('', ''),				// latin
		'lb' => array('', 'fr'),				// luxembourgeois
		'ln' => array('', ''),					// lingala
		'lo' => array('', ''), 					// lao
		'lt' => array('', ''),				// lituanien
		'lu' => array('', ''),					// luba-katanga
		'lv' => array('', ''),				// letton
		'man' => array('', ''),				// mandingue
		'mfv' => array('', ''), 				// manjaque - iso-639-3
		'mg' => array('', ''),					// malgache
		'mi' => array('', ''),				// maori
		'mk' => array('', ''),				// macédonien
		'ml' => array('', ''),					// malayalam
		'mn' => array('', ''),				// mongol
		'mo' => array('', 'ro'),				// moldave ??? normalement c'est ro comme le roumain
		'mos' => array('', ''),					// moré - iso 639-2
		'mr' => array('', ''),				// marathe
		'ms' => array('', ''),					// malais
		'mt' => array('', 'en'),				// maltais
		'my' => array('', ''),				// birman
		'na' => array('', ''),					// nauruan
		'nap' => array('', 'it'),				// napolitain - iso 639-2
		'ne' => array('', ''),					// népalais
		'nqo' => array('', ''), 				// n’ko - iso 639-3
		'nl' => array('nl', ''),				// néerlandais
		'no' => array('', ''),				// norvégien
		'nb' => array('', ''),				// norvégien bokmål
		'nn' => array('', ''),				// norvégien nynorsk
		'oc' => array('', 'fr'),				// occitan
		'oc_lnc' => array('', 'fr'),			// occitan languedocien
		'oc_ni' => array('', 'fr'),				// occitan niçard
		'oc_ni_la' => array('', 'fr'),			// occitan niçard
		'oc_prv' => array('', 'fr'),			// occitan provençal
		'oc_gsc' => array('', 'fr'),			// occitan gascon
		'oc_lms' => array('', 'fr'),			// occitan limousin
		'oc_auv' => array('', 'fr'),			// occitan auvergnat
		'oc_va' => array('', 'fr'),				// occitan vivaro-alpin
		'om' => array('', ''),					// galla
		'or' => array('', ''),					// oriya
		'pa' => array('', ''),				// pendjabi
		'pbb' => array('', ''),					// Nasa Yuwe (páez) - iso 639-3
		'pl' => array('pl', ''),				// polonais
		'ps' => array('', ''),				// pachto
		'pt' => array('pt', ''),				// portugais
		'pt_br' => array('', 'pt'),				// portugais brésilien
		'qu' => array('', ''),					// quechua
		'rm' => array('', ''),					// romanche
		'rn' => array('', ''),					// rundi
		'ro' => array('ro', ''),				// roumain
		'roa' => array('', 'fr'),				// langues romanes (ch'ti) - iso 639-2
		'ru' => array('ru', ''),				// russe
		'rw' => array('', ''),					// rwanda
		'sa' => array('', ''),					// sanskrit
		'sc' => array('', 'it'),				// sarde
		'scn' => array('', 'it'),				// sicilien - iso 639-2
		'sd' => array('', ''),					// sindhi
		'sg' => array('', ''),					// sango
		'sh' => array('', ''),				// serbo-croate
		'sh_latn' => array('', ''),			// serbo-croate latin
		'sh_cyrl' => array('', ''),			// serbo-croate cyrillique
		'si' => array('', ''),					// singhalais
		'sk' => array('', ''),				// slovaque
		'sl' => array('', ''),				// slovène
		'sm' => array('', 'en'),					// samoan
		'sn' => array('', ''),					// shona
		'so' => array('', ''),					// somali
		'sq' => array('', ''), 				// albanais
		'sr' => array('', ''),				// serbe
		'src' => array('', 'it'), 				// sarde logoudorien - iso 639-3
		'sro' => array('', 'it'), 				// sarde campidanien - iso 639-3
		'ss' => array('', ''),					// swati
		'st' => array('', ''),					// sotho du Sud
		'su' => array('', ''),					// soundanais
		'sv' => array('se', ''),				// suédois
		'sw' => array('', ''),				// swahili
		'ta' => array('', ''), 					// tamoul
		'te' => array('', ''),					// télougou
		'tg' => array('', ''),					// tadjik
		'th' => array('', ''),				// thaï
		'ti' => array('', ''),					// tigrigna
		'tk' => array('', ''),				// turkmène
		'tl' => array('', ''),				// tagalog
		'tn' => array('', ''),					// tswana
		'to' => array('', ''),					// tongan (Îles Tonga)
		'tr' => array('tr', ''),				// turc
		'ts' => array('', ''),					// tsonga
		'tt' => array('', ''),				// tatar
		'tw' => array('', ''),					// twi
		'ty' => array('', 'fr'),			 	// tahitien
		'ug' => array('', ''),					// ouïgour
		'uk' => array('ua', ''),				// ukrainien
		'ur' => array('', ''),					// ourdou
		'uz' => array('', ''),				// ouszbek
		'vi' => array('', ''),				// vietnamien
		'vo' => array('', ''),					// volapük
		'wa' => array('', 'fr'),				// wallon
		'wo' => array('', ''),				// wolof
		'xh' => array('', ''),					// xhosa
		'yi' => array('', ''),				// yiddish
		'yo' => array('', ''),					// yoruba
		'za' => array('', 'zh_cn'),				// zhuang
		'zh' => array('zh_cn', ''), 				// chinois (ecriture simplifiee)
		'zh_tw' => array('zh_tw', ''), 			// chinois taiwan (ecriture traditionnelle)
		'zu' => array('', '')					// zoulou
	);

	$code = _RAINETTE_OWM_LANGUE_DEFAUT;
	if (array_key_exists($langue,  $langue2owm)) {
		if ($c0 = $langue2owm[$langue][0])
			$code = strtoupper($c0);
		else
			$code = strtoupper($langue2owm[$langue][1]);
	}

	return $code;
}

?>