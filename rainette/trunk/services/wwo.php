<?php

define('_RAINETTE_WWO_URL_BASE', 'http://free.worldweatheronline.com/feed/weather.ashx');
define('_RAINETTE_WWO_JOURS_PREVISIONS', 5);

function wwo_service2cache($lieu, $mode) {

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'wwo');
	$fichier_cache = $dir . str_replace(array(',', '+', '.'), '-', $lieu) . "_" . $mode . ".txt";

	return $fichier_cache;
}

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

function wwo_url2flux($url) {

	include_spip('inc/distant');
	$flux = recuperer_page($url);

	include_spip('inc/rainette_utils');
	$xml = @simplexml2array(simplexml_load_string($flux));

	return $xml;
}

function wwo_code2icone($meteo) {
	static $wwo2weather = array(
							'395'=> array('41','46'),
							'392'=> array('41','46'),
							'389'=> array('38','47'),
							'386'=> array('37','47'),
							'377'=> array('6','6'),
							'374'=> array('6','6'),
							'371'=> array('14','14'),
							'368'=> array('13','13'),
							'365'=> array('6','6'),
							'362'=> array('6','6'),
							'359'=> array('11','11'),
							'356'=> array('11','11'),
							'353'=> array('9','9'),
							'350'=> array('18','18'),
							'338'=> array('16','16'),
							'335'=> array('16','16'),
							'332'=> array('14','14'),
							'329'=> array('14','14'),
							'326'=> array('13','13'),
							'323'=> array('13','13'),
							'320'=> array('18','18'),
							'317'=> array('18','18'),
							'314'=> array('8','8'),
							'311'=> array('8','8'),
							'308'=> array('40','40'),
							'305'=> array('39','45'),
							'302'=> array('11','11'),
							'299'=> array('39','45'),
							'296'=> array('9','9'),
							'293'=> array('9','9'),
							'284'=> array('10','10'),
							'281'=> array('9','9'),
							'266'=> array('9','9'),
							'263'=> array('9','9'),
							'260'=> array('20','20'),
							'248'=> array('20','20'),
							'230'=> array('16','16'),
							'227'=> array('15','15'),
							'200'=> array('38','47'),
							'185'=> array('10','10'),
							'182'=> array('18','18'),
							'179'=> array('16','16'),
							'176'=> array('40','49'),
							'143'=> array('20','20'),
							'122'=> array('26','26'),
							'119'=> array('28','27'),
							'116'=> array('30','29'),
							'113'=> array('32','31'));

	$icone = 'na';
	if (array_key_exists($meteo,  $wwo2weather))
		$icone = strval($wwo2weather[$meteo][0]);
	return $icone;
}

/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * ne gere pas encore le jour et la nuit de la date courante suivant l'heure!!!!
 * @param array $xml
 * @return array
 */
function wwo_xml2previsions($xml){
	$tableau = array();
	$n = spip_xml_match_nodes(",^dayf,",$xml,$previsions);
	if ($n==1){
		$previsions = reset($previsions['dayf']);
		// recuperer la date de debut des previsions (c'est la date de derniere maj)
		$date_maj = $previsions['lsup'][0];
		$date_maj = strtotime(preg_replace(',\slocal\s*time\s*,ims','',$date_maj));
		$index = 0;
		foreach($previsions as $day=>$p){
			if (preg_match(",day\s*d=['\"?]([0-9]+),Uims",$day,$regs)){
				$date_stamp = $date_maj+$regs[1]*24*3600;
				$p = reset($p);
				// Index du jour et date du jour
				$tableau[$index]['index'] = $index;
				$tableau[$index]['date'] = date('Y-m-d',$date_stamp);
				// Date complete des lever/coucher du soleil
				$date = getdate($date_stamp);
				$heure = getdate(strtotime($p['sunr'][0]));
				$sun = mktime($heure['hours'],$heure['minutes'],0,$date['mon'],$date['mday'],$date['year']);
				$tableau[$index]['lever_soleil'] = date('Y-m-d H:i:s',$sun);
				$heure = getdate(strtotime($p['suns'][0]));
				$sun = mktime($heure['hours'],$heure['minutes'],0,$date['mon'],$date['mday'],$date['year']);
				$tableau[$index]['coucher_soleil'] = date('Y-m-d H:i:s',$sun);
				// Previsions du jour
				$tableau[$index]['temperature_jour'] = intval($p['hi'][0]) ? intval($p['hi'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['code_icone_jour'] = intval($p['part p="d"'][0]['icon'][0]) ? intval($p['part p="d"'][0]['icon'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['vitesse_vent_jour'] = intval($p['part p="d"'][0]['wind'][0]['s'][0]) ? intval($p['part p="d"'][0]['wind'][0]['s'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['angle_vent_jour'] = $p['part p="d"'][0]['wind'][0]['d'][0];
				$tableau[$index]['direction_vent_jour'] = $p['part p="d"'][0]['wind'][0]['t'][0];
				$tableau[$index]['risque_precipitation_jour'] = intval($p['part p="d"'][0]['ppcp'][0]);
				$tableau[$index]['humidite_jour'] = intval($p['part p="d"'][0]['hmid'][0]) ? intval($p['part p="d"'][0]['hmid'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				// Previsions de la nuit
				$tableau[$index]['temperature_nuit'] = intval($p['low'][0]) ? intval($p['low'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['code_icone_nuit'] = intval($p['part p="n"'][0]['icon'][0]) ? intval($p['part p="n"'][0]['icon'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['vitesse_vent_nuit'] = intval($p['part p="n"'][0]['wind'][0]['s'][0]) ? intval($p['part p="n"'][0]['wind'][0]['s'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['angle_vent_nuit'] = $p['part p="n"'][0]['wind'][0]['d'][0];
				$tableau[$index]['direction_vent_nuit'] = $p['part p="n"'][0]['wind'][0]['t'][0];
				$tableau[$index]['risque_precipitation_nuit'] = intval($p['part p="n"'][0]['ppcp'][0]);
				$tableau[$index]['humidite_nuit'] = intval($p['part p="n"'][0]['hmid'][0]) ? intval($p['part p="n"'][0]['hmid'][0]) : _RAINETTE_VALEUR_INDETERMINEE;

				$index += 1;
			}
		}
		// On stocke en fin de tableau la date de derniere mise a jour
		$tableau[$index]['derniere_maj'] = date('Y-m-d H:i:s',$date_maj);
		// trier par date
		ksort($tableau);
	}
	return $tableau;
}

function wwo_xml2conditions($xml){
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($xml['children']['current_condition'][0]['children'])) {
		$conditions = $xml['children']['current_condition'][0]['children'];

		// Date d'observation
		$date_maj = (isset($conditions['localobsdatetime'])) ? ', ' . $conditions['localobsdatetime'][0]['text'] : '';
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', strtotime($date_maj));
		// Station d'observation
		$tableau['station'] = '';

		// Liste des conditions meteo extraite dans le systeme metrique
		$tableau['vitesse_vent'] = (isset($conditions['windspeedkmph'])) ? intval($conditions['windspeedkmph'][0]['text']) : '';
		$tableau['angle_vent'] = (isset($conditions['winddirdegree'])) ? intval($conditions['winddirdegree'][0]['text']) : '';
		$tableau['direction_vent'] = (isset($conditions['winddir16point'])) ? $conditions['winddir16point'][0]['text'] : '';

		$tableau['temperature_reelle'] = (isset($conditions['temp_c'])) ? intval($conditions['temp_c'][0]['text']) : '';
		$tableau['temperature_ressentie'] = temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']);

		$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity'][0]['text']) : '';
		$tableau['point_rosee'] = '';

		$tableau['pression'] = (isset($conditions['pressure'])) ? intval($conditions['pressure'][0]['text']) : '';
		$tableau['tendance_pression'] = '';

		$tableau['visibilite'] = (isset($conditions['visibility'])) ? intval($conditions['visibility'][0]['text']) : '';

		$tableau['code_icone'] = (isset($conditions['weathercode'])) ? intval($conditions['weathercode'][0]['text']) : '';
		$tableau['url_icone'] = (isset($conditions['weathericonurl'])) ? $conditions['weathericonurl'][0]['text'] : '';
		$tableau['desc_icone'] = (isset($conditions['weatherdesc'])) ? $conditions['weatherdesc'][0]['text'] : '';

		// On convertit les informations exprimees en systeme metrique dans le systeme US si besoin
		include_spip('inc/config');
		$unite = lire_config('rainette/wwo/unite');
		if ($unite == 's') {
			include_spip('inc/rainette_utils');
			$tableau['temperature_reelle'] = (isset($conditions['temp_f']))
				? intval($conditions['temp_f'][0]['text'])
				: celsius2farenheit($tableau['temperature_reelle']);
			$tableau['temperature_ressentie'] = celsius2farenheit($tableau['temperature_ressentie']);
			$tableau['vitesse_vent'] = (isset($conditions['windspeedmiles']))
				? intval($conditions['windspeedmiles'][0]['text'])
				: kilometre2mile($tableau['vitesse_vent']);
			$tableau['visibilite'] = kilometre2mile($tableau['visibilite']);
			$tableau['pression'] = millibar2inch($tableau['pression']);
		}
	}

	return $tableau;
}

function wwo_xml2infos($xml, $lieu){
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($xml['children']['nearest_area'][0]['children'])) {
		$infos = $xml['children']['nearest_area'][0]['children'];

		if (isset($infos['areaname'])) {
			$tableau['ville'] = $infos['areaname'][0]['text'];
			$tableau['ville'] .= (isset($infos['country'])) ? ', ' . $infos['country'][0]['text'] : '';
		}
		$tableau['region'] = (isset($infos['region'])) ? $infos['region'][0]['text'] : '';

		$tableau['longitude'] = (isset($infos['longitude'])) ? floatval($infos['longitude'][0]['text']) : '';
		$tableau['latitude'] = (isset($infos['latitude'])) ? floatval($infos['latitude'][0]['text']) : '';

		$tableau['population'] = (isset($infos['population'])) ? intval($infos['population'][0]['text']) : '';
		$tableau['zone'] = '';
	}

	return $tableau;
}

?>