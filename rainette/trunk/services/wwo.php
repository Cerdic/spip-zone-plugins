<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_WWO_URL_BASE'))
	define('_RAINETTE_WWO_URL_BASE', 'http://api.worldweatheronline.com/free/v1/weather.ashx');
if (!defined('_RAINETTE_WWO_JOURS_PREVISIONS'))
	define('_RAINETTE_WWO_JOURS_PREVISIONS', 5);

/**
 * @param string	$lieu
 * @param string	$mode
 * @return string
 */
function wwo_service2cache($lieu, $mode) {

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'wwo');
	$fichier_cache = $dir . str_replace(array(',', '+', '.'), '-', $lieu) . "_" . $mode . ".txt";

	return $fichier_cache;
}

/**
 * @param string	$lieu
 * @param string	$mode
 * @return string
 */
function wwo_service2url($lieu, $mode) {

	include_spip('inc/config');
	$cle = lire_config('rainette/wwo/inscription');

	$url = _RAINETTE_WWO_URL_BASE
		.  '?key=' . $cle
		.  '&format=xml&extra=localObsTime'
		.  '&q=' . str_replace(' ', '', trim($lieu));
	if ($mode == 'infos') {
		$url .= '&includeLocation=yes&cc=no&fx=no';
	}
	else {
		$url .= ($mode == 'previsions') ? '&cc=no&fx=yes&num_of_days=' . _RAINETTE_WWO_JOURS_PREVISIONS : '&cc=yes&fx=no';
	}

	return $url;
}

/**
 * @param string $mode
 * @return int
 */
function wwo_service2reload_time($mode) {

	static $reload = array('conditions' => 10800, 'previsions' => 14400);

	return $reload[$mode];
}

/**
 * @param string	$url
 * @return array
 */
function wwo_url2flux($url) {

	include_spip('inc/phraser');
	$flux = url2flux_xml($url, false);

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
function wwo_flux2previsions($flux, $lieu) {
	$tableau = array();

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

/**
 * @param array 	$flux
 * @param string	$lieu
 * @return array
 */
function wwo_flux2conditions($flux, $lieu) {
	$tableau = array();
	include_spip('inc/convertir');

	// On stocke les informations disponibles dans un tableau standard
	if (isset($flux['children']['current_condition'][0]['children'])) {
		$conditions = $flux['children']['current_condition'][0]['children'];

		// Date d'observation
		$date_maj = (isset($conditions['localobsdatetime'])) ? strtotime($conditions['localobsdatetime'][0]['text']) : '';
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		$tableau['station'] = '';

		// Liste des conditions meteo extraite dans le systeme metrique
		$tableau['vitesse_vent'] = (isset($conditions['windspeedkmph'])) ? floatval($conditions['windspeedkmph'][0]['text']) : '';
		$tableau['angle_vent'] = (isset($conditions['winddirdegree'])) ? intval($conditions['winddirdegree'][0]['text']) : '';
		$tableau['direction_vent'] = (isset($conditions['winddir16point'])) ? $conditions['winddir16point'][0]['text'] : '';

		$tableau['temperature_reelle'] = (isset($conditions['temp_c'])) ? intval($conditions['temp_c'][0]['text']) : '';
		$tableau['temperature_ressentie'] = (isset($conditions['temp_c'])) ? temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']) : '';

		$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity'][0]['text']) : '';
		$tableau['point_rosee'] = '';

		$tableau['pression'] = (isset($conditions['pressure'])) ? floatval($conditions['pressure'][0]['text']) : '';
		$tableau['tendance_pression'] = '';

		$tableau['visibilite'] = (isset($conditions['visibility'])) ? floatval($conditions['visibility'][0]['text']) : '';

		// On convertit les informations exprimees en systeme metrique dans le systeme US si besoin
		include_spip('inc/config');
		$unite = lire_config('rainette/wwo/unite', 'm');
		if ($unite == 's') {
			$tableau['temperature_reelle'] = (isset($conditions['temp_f']))
				? intval($conditions['temp_f'][0]['text'])
				: intval(round(celsius2farenheit($tableau['temperature_reelle']), 0));
			$tableau['temperature_ressentie'] = intval(round(celsius2farenheit($tableau['temperature_ressentie']), 0));
			$tableau['vitesse_vent'] = (isset($conditions['windspeedmiles']))
				? floatval($conditions['windspeedmiles'][0]['text'])
				: kilometre2mile($tableau['vitesse_vent']);
			$tableau['visibilite'] = kilometre2mile($tableau['visibilite']);
			$tableau['pression'] = millibar2inch($tableau['pression']);
		}

		// Code meteo, resume et icone natifs au service
		$tableau['code_meteo'] = (isset($conditions['weathercode'])) ? intval($conditions['weathercode'][0]['text']) : '';
		$tableau['icon_meteo'] = (isset($conditions['weathericonurl'])) ? $conditions['weathericonurl'][0]['text'] : '';
		$tableau['desc_meteo'] = (isset($conditions['weatherdesc'])) ? $conditions['weatherdesc'][0]['text'] : '';

		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service aucun indicateur n'est disponible
		// -> on utilise le nom de l'icone qui contient l'indication "night"
		// TODO : a verifier si autre moyen
		$icone = basename($tableau['icon_meteo']);
		if (strpos($icone, '_night') === false)
			$tableau['periode'] = 0; // jour
		else
			$tableau['periode'] = 1; // nuit

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		$condition = lire_config('rainette/wwo/condition', 'wwo');
		if ($condition == 'wwo') {
			// On affiche les conditions natives fournies par le service.
			// Pour le resume, wwo ne fournit pas de traduction : on stocke donc le code meteo afin
			// de le traduire à partir des fichiers de langue SPIP.
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$tableau['icone']['url'] = copie_locale($tableau['icon_meteo']);
			$tableau['resume'] = $tableau['code_meteo'];
		}
		else {
			// On affiche les conditions traduites dans le systeme weather.com
			$meteo = meteo_wwo2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

/**
 * @param array 	$flux
 * @param string	$lieu
 * @return array
 */
function wwo_flux2infos($flux, $lieu){
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($flux['children']['nearest_area'][0]['children'])) {
		$infos = $flux['children']['nearest_area'][0]['children'];

		if (isset($infos['areaname'])) {
			$tableau['ville'] = $infos['areaname'][0]['text'];
			$tableau['ville'] .= (isset($infos['country'])) ? ', ' . $infos['country'][0]['text'] : '';
		}
		$tableau['region'] = (isset($infos['region'])) ? $infos['region'][0]['text'] : '';

		$tableau['longitude'] = (isset($infos['longitude'])) ? floatval($infos['longitude'][0]['text']) : '';
		$tableau['latitude'] = (isset($infos['latitude'])) ? floatval($infos['latitude'][0]['text']) : '';

		$tableau['population'] = (isset($infos['population'])) ? intval($infos['population'][0]['text']) : '';
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

/**
 * @return array
 */
function wwo_service2credits() {

	$credits = array('logo' => '');
	$credits['lien'] = 'http://www.worldweatheronline.com/';
	$credits['titre'] = 'Free local weather content provider';

	return $credits;
}

/**
 * @internal
 *
 * @param string $meteo
 * @param int $periode
 * @return string
 */
function meteo_wwo2weather($meteo, $periode=0) {
	static $wwo2weather = array(
							395=> array(41,46),
							392=> array(41,46),
							389=> array(38,47),
							386=> array(37,47),
							377=> array(6,6),
							374=> array(6,6),
							371=> array(14,14),
							368=> array(13,13),
							365=> array(6,6),
							362=> array(6,6),
							359=> array(11,11),
							356=> array(11,11),
							353=> array(9,9),
							350=> array(18,18),
							338=> array(16,16),
							335=> array(16,16),
							332=> array(14,14),
							329=> array(14,14),
							326=> array(13,13),
							323=> array(13,13),
							320=> array(18,18),
							317=> array(18,18),
							314=> array(8,8),
							311=> array(8,8),
							308=> array(40,40),
							305=> array(39,45),
							302=> array(11,11),
							299=> array(39,45),
							296=> array(9,9),
							293=> array(9,9),
							284=> array(10,10),
							281=> array(9,9),
							266=> array(9,9),
							263=> array(9,9),
							260=> array(20,20),
							248=> array(20,20),
							230=> array(16,16),
							227=> array(15,15),
							200=> array(38,47),
							185=> array(10,10),
							182=> array(18,18),
							179=> array(16,16),
							176=> array(40,49),
							143=> array(20,20),
							122=> array(26,26),
							119=> array(28,27),
							116=> array(30,29),
							113=> array(32,31));

	$icone = 'na';
	if (array_key_exists($meteo,  $wwo2weather))
		$icone = strval($wwo2weather[$meteo][$periode]);
	return $icone;
}

?>