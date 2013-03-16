<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_RAINETTE_WEATHER_URL_BASE', 'http://xml.weather.com/weather/local/');
define('_RAINETTE_WEATHER_JOURS_PREVISION', 10);

function weather_service2cache($lieu, $mode) {

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'weather');
	$f = $dir . strtoupper($lieu) . "_" . $mode . ".txt";

	return $f;
}


function weather_service2url($lieu, $mode) {

	include_spip('inc/config');
	$unite = lire_config('rainette/weather/unite', 'm');

	$url = _RAINETTE_WEATHER_URL_BASE . strtoupper($lieu) . '?unit=' . $unite;
	if ($mode != 'infos') {
		$url .= ($mode == 'previsions') ? '&dayf=' . _RAINETTE_WEATHER_JOURS_PREVISION : '&cc=*';
	}

	return $url;
}


function weather_service2reload_time($mode) {

	static $reload = array('conditions' => 1800, 'previsions' => 7200);

	return $reload[$mode];
}


function weather_url2flux($url) {

	include_spip('inc/xml');
	$flux = spip_xml_load($url);

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
function weather_flux2previsions($flux, $lieu) {
	$tableau = array();
	$index = 0;

	$n = spip_xml_match_nodes(",^dayf,",$flux,$previsions);
	if ($n==1){
		$previsions = reset($previsions['dayf']);
		// recuperer la date de debut des previsions (c'est la date de derniere maj)
		$date_maj = $previsions['lsup'][0];
		$date_maj = strtotime(preg_replace(',\slocal\s*time\s*,ims','',$date_maj));
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
				$tableau[$index]['temperature_jour'] = intval($p['hi'][0]) ? intval($p['hi'][0]) : 'N/D';
				$tableau[$index]['code_icone_jour'] = intval($p['part p="d"'][0]['icon'][0]) ? intval($p['part p="d"'][0]['icon'][0]) : 'N/D';
				$tableau[$index]['vitesse_vent_jour'] = intval($p['part p="d"'][0]['wind'][0]['s'][0]) ? intval($p['part p="d"'][0]['wind'][0]['s'][0]) : 'N/D';
				$tableau[$index]['angle_vent_jour'] = $p['part p="d"'][0]['wind'][0]['d'][0];
				$tableau[$index]['direction_vent_jour'] = $p['part p="d"'][0]['wind'][0]['t'][0];
				$tableau[$index]['risque_precipitation_jour'] = intval($p['part p="d"'][0]['ppcp'][0]);
				$tableau[$index]['humidite_jour'] = intval($p['part p="d"'][0]['hmid'][0]) ? intval($p['part p="d"'][0]['hmid'][0]) : 'N/D';
				// Previsions de la nuit
				$tableau[$index]['temperature_nuit'] = intval($p['low'][0]) ? intval($p['low'][0]) : 'N/D';
				$tableau[$index]['code_icone_nuit'] = intval($p['part p="n"'][0]['icon'][0]) ? intval($p['part p="n"'][0]['icon'][0]) : 'N/D';
				$tableau[$index]['vitesse_vent_nuit'] = intval($p['part p="n"'][0]['wind'][0]['s'][0]) ? intval($p['part p="n"'][0]['wind'][0]['s'][0]) : 'N/D';
				$tableau[$index]['angle_vent_nuit'] = $p['part p="n"'][0]['wind'][0]['d'][0];
				$tableau[$index]['direction_vent_nuit'] = $p['part p="n"'][0]['wind'][0]['t'][0];
				$tableau[$index]['risque_precipitation_nuit'] = intval($p['part p="n"'][0]['ppcp'][0]);
				$tableau[$index]['humidite_nuit'] = intval($p['part p="n"'][0]['hmid'][0]) ? intval($p['part p="n"'][0]['hmid'][0]) : 'N/D';

				$index += 1;
			}
		}
		// On stocke en fin de tableau la date de derniere mise a jour
		$tableau[$index]['derniere_maj'] = date('Y-m-d H:i:s',$date_maj);
	}

	// Traitement des erreurs de flux
	$tableau[$index]['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}


function weather_flux2conditions($flux, $lieu) {
	$tableau = array();
	$n = spip_xml_match_nodes(",^cc,",$flux,$conditions);
	if ($n==1){
		$conditions = reset($conditions['cc']);
		// recuperer la date de derniere mise a jour des conditions
		if ($conditions) {
			// Date d'observation
			$date_maj = $conditions['lsup'][0];
			$date_maj = strtotime(preg_replace(',\slocal\s*time\s*,ims','',$date_maj));
			$tableau['derniere_maj'] = date('Y-m-d H:i:s',$date_maj);
			// station d'observation (peut etre differente de la ville)
			$tableau['station'] = $conditions['obst'][0];

			// Liste des conditions meteo
			$tableau['vitesse_vent'] = floatval($conditions['wind'][0]['s'][0]);
			$tableau['angle_vent'] = intval($conditions['wind'][0]['d'][0]);
			$tableau['direction_vent'] = $conditions['wind'][0]['t'][0];

			$tableau['temperature_reelle'] = intval($conditions['tmp'][0]);
			$tableau['temperature_ressentie'] = intval($conditions['flik'][0]);

			$tableau['humidite'] = intval($conditions['hmid'][0]);
			$tableau['point_rosee'] = intval($conditions['dewp'][0]);

			$tableau['pression'] = floatval($conditions['bar'][0]['r'][0]);
			$tableau['tendance_pression'] = $conditions['bar'][0]['d'][0];

			$tableau['visibilite'] = floatval($conditions['vis'][0]);

			$tableau['code_meteo'] = intval($conditions['icon'][0]);
			$tableau['icon_meteo'] = '';
			$tableau['desc_meteo'] = $conditions['t'][0];

			// TODO : determiner la periode jour ou nuit
			$tableau['periode'] = '';

			// La traduction du resume dans la bonne langue est toujours faite par les fichiers de langue SPIP
			// car l'API ne permet pas de choisir la langue. On ne stocke donc que le code meteo
			$tableau['icone'] = $tableau['code_meteo'];
			$tableau['code_icone'] = $tableau['code_meteo']; // compat ascendante
			$tableau['resume'] = $tableau['code_meteo'];
		}
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

function weather_flux2infos($flux, $lieu){
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	$regexp = 'loc id=\"' . $lieu . '\"';
	$n = spip_xml_match_nodes(",^$regexp,", $flux, $infos);
	if ($n==1){
		$infos = reset($infos['loc id="' . $lieu . '"']);
		// recuperer la date de debut des conditions
		$tableau['ville'] = $infos['dnam'][0];
		$tableau['region'] = '';

		$tableau['longitude'] = floatval($infos['lon'][0]);
		$tableau['latitude'] = floatval($infos['lat'][0]);

		$tableau['population'] = '';
		$tableau['zone'] = intval($infos['zone'][0]);
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

function weather_service2credits() {

	$credits = array('titre' => '', 'logo' => '');
	$credits['lien'] = 'http://www.weather.com/';

	return $credits;
}

?>
