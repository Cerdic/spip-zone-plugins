<?php
// free.worldweatheronline.com/feed/weather.ashx?key=30e3b46523060112120708&q=Paris,France&cc=no&fx=yes&format=xml&num_of_days=5&extra=localObsTime&includeLocation=yes
define('_RAINETTE_WWO_URL_BASE', 'http://free.worldweatheronline.com/feed/weather.ashx');
define('_RAINETTE_WWO_JOURS_PREVISIONS', 5);

function wwo_service2cache($lieu, $mode) {

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'wwo');
	$fichier_cache = $dir . str_replace(array(',', '+', '.'), '-', $lieu) . "_" . $mode . ".txt";

	return $fichier_cache;
}

function wwo_service2url($lieu, $mode) {

	$url = _RAINETTE_WWO_URL_BASE
		.  '&format=xml&extra=localObsTime'
		.  'q=' . str_replace(' ', '', trim($lieu))
		.  '?key=' . '30e3b46523060112120708';
	if ($mode == 'infos') {
		$url .= '&includeLocation=yes';
	}
	else {
		$url .= ($mode == 'previsions') ? '&cc=no&fx=yes&num_of_days=' . _RAINETTE_WWO_JOURS_PREVISIONS : '&cc=yes&fx=no';
	}

	return $url;
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
	$n = spip_xml_match_nodes(",^cc,",$xml,$conditions);
	if ($n==1){
		$conditions = reset($conditions['cc']);
		// recuperer la date de derniere mise a jour des conditions
		if ($conditions) {
			$date_maj = $conditions['lsup'][0];
			$date_maj = strtotime(preg_replace(',\slocal\s*time\s*,ims','',$date_maj));
			$tableau['derniere_maj'] = date('Y-m-d H:i:s',$date_maj);
			// station d'observation (peut etre differente de la ville)
			$tableau['station'] = $conditions['obst'][0];
			// Liste des conditions meteo
			$tableau['temperature_reelle'] = intval($conditions['tmp'][0]);
			$tableau['temperature_ressentie'] = intval($conditions['flik'][0]);
			$tableau['code_icone'] = intval($conditions['icon'][0]);
			$tableau['pression'] = intval($conditions['bar'][0]['r'][0]);
			$tableau['tendance_pression'] = $conditions['bar'][0]['d'][0];
			$tableau['vitesse_vent'] = intval($conditions['wind'][0]['s'][0]);
			$tableau['angle_vent'] = intval($conditions['wind'][0]['d'][0]);
			$tableau['direction_vent'] = $conditions['wind'][0]['t'][0];
			$tableau['humidite'] = intval($conditions['hmid'][0]);
			$tableau['point_rosee'] = intval($conditions['dewp'][0]);
			$tableau['visibilite'] = intval($conditions['vis'][0]);
		}
	}
	return $tableau;
}

function wwo_xml2infos($xml, $lieu){
	$tableau = array();
	$regexp = 'loc id=\"'.$lieu.'\"';
	$n = spip_xml_match_nodes(",^$regexp,",$xml,$infos);
	if ($n==1){
		$infos = reset($infos['loc id="'.$lieu.'"']);
		// recuperer la date de debut des conditions
		$tableau['code_meteo'] = $lieu;
		$tableau['ville'] = $infos['dnam'][0];
		$tableau['longitude'] = floatval($infos['lon'][0]);
		$tableau['latitude'] = floatval($infos['lat'][0]);
		$tableau['zone'] = intval($infos['zone'][0]);
	}
	return $tableau;
}

?>