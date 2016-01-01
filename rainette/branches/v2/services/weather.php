<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service l'ancien service Weather.com (weather).
 * Ce service fournit des données au format XML uniquement.
 *
 * @package SPIP\RAINETTE\WEATHER
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_WEATHER_URL_BASE'))
	define('_RAINETTE_WEATHER_URL_BASE', 'http://xml.weather.com/weather/local/');
if (!defined('_RAINETTE_WEATHER_JOURS_PREVISIONS'))
	define('_RAINETTE_WEATHER_JOURS_PREVISIONS', 10);

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
		$url .= ($mode == 'previsions') ? '&dayf=' . _RAINETTE_WEATHER_JOURS_PREVISIONS : '&cc=*';
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
	$date_maj = '';

	$n = spip_xml_match_nodes(",^dayf,",$flux,$previsions);
	if ($n==1){
		$previsions = reset($previsions['dayf']);
		// recuperer la date de debut des previsions (c'est la date de derniere maj)
		$date_service = $previsions['lsup'][0];
		// Certaines abréviations de fuseau ne sont pas reconnues par date_create suivant la configuration du serveur
		// http. On supprime cette information avant le date_create qui produit un timezone erroné mais qui ne genera
		// pas le format ensuite.
		if (!$date_maj = date_create($date_service)) {
			$elements_date = explode(' ', $date_service);
			array_pop($elements_date);
			$date_service = implode(' ', $elements_date);
			$date_maj = date_create($date_service);
		}
		// On réinitialise la date de prévision à partir juste du jour afin d'avoir un time à 0
		foreach($previsions as $day=>$p){
			if (preg_match(",day\s*d=['\"?]([0-9]+),Uims",$day,$regs)){
				// Index du jour et date du jour
				$tableau[$index]['index'] = $index;

				// Date du jour
				$date_prevision = date_create($elements_date[0]);
				$interval = new DateInterval("P{$regs[1]}D");
				date_add($date_prevision, $interval);
				$tableau[$index]['date'] = date_format($date_prevision, 'Y-m-d');

				$p = reset($p);

				// Date complete des lever/coucher du soleil
				list($heure,$am_pm) = explode(' ', $p['sunr'][0]);
				list($heure, $minute) = explode(':', $heure);
				$minute = strval($minute);
				$heure = ($am_pm == 'PM') ? strval($heure) + 12 : strval($heure);
				$interval = new DateInterval("PT${heure}H${minute}M");
				$date_soleil = clone $date_prevision;
				date_add($date_soleil, $interval);
				$tableau[$index]['lever_soleil'] = date_format($date_soleil, 'Y-m-d H:i:s');

				list($heure,$am_pm) = explode(' ', $p['suns'][0]);
				list($heure, $minute) = explode(':', $heure);
				$minute = strval($minute);
				$heure = ($am_pm == 'PM') ? strval($heure) + 12 : strval($heure);
				$interval = new DateInterval("PT${heure}H${minute}M");
				$date_soleil = clone $date_prevision;
				date_add($date_soleil, $interval);
				$tableau[$index]['coucher_soleil'] = date_format($date_soleil, 'Y-m-d H:i:s');

				// Previsions du jour
				$tableau[$index][0]['temperature_max'] = intval($p['hi'][0]) ? floatval($p['hi'][0]) : '';
				$tableau[$index][0]['temperature_min'] = intval($p['low'][0]) ? floatval($p['low'][0]) : '';
				$tableau[$index][0]['vitesse_vent'] = intval($p['part p="d"'][0]['wind'][0]['s'][0]) ? floatval($p['part p="d"'][0]['wind'][0]['s'][0]) : '';
				$tableau[$index][0]['angle_vent'] = $p['part p="d"'][0]['wind'][0]['d'][0];
				$tableau[$index][0]['direction_vent'] = $p['part p="d"'][0]['wind'][0]['t'][0];
				$tableau[$index][0]['risque_precipitation'] = intval($p['part p="d"'][0]['ppcp'][0]);
				$tableau[$index][0]['precipitation'] = NULL;
				$tableau[$index][0]['humidite'] = intval($p['part p="d"'][0]['hmid'][0]) ? intval($p['part p="d"'][0]['hmid'][0]) : '';

				$tableau[$index][0]['code_meteo'] = intval($p['part p="d"'][0]['icon'][0]) ? intval($p['part p="d"'][0]['icon'][0]) : '';
				$tableau[$index][0]['icon_meteo'] = NULL;
				$tableau[$index][0]['desc_meteo'] = NULL;

				// La traduction du resume dans la bonne langue est toujours faite par les fichiers de langue SPIP
				// car l'API ne permet pas de choisir la langue. On ne stocke donc que le code meteo
				$tableau[$index][0]['icone'] = $tableau[$index][0]['code_meteo'];
				$tableau[$index][0]['resume'] = $tableau[$index][0]['code_meteo'];

				// Previsions de la nuit
				$tableau[$index][1]['temperature_max'] = intval($p['low'][0]) ? floatval($p['low'][0]) : '';
				$tableau[$index][1]['temperature_min'] = NULL;
				$tableau[$index][1]['vitesse_vent'] = intval($p['part p="n"'][0]['wind'][0]['s'][0]) ? floatval($p['part p="n"'][0]['wind'][0]['s'][0]) : '';
				$tableau[$index][1]['angle_vent'] = $p['part p="n"'][0]['wind'][0]['d'][0];
				$tableau[$index][1]['direction_vent'] = $p['part p="n"'][0]['wind'][0]['t'][0];
				$tableau[$index][1]['risque_precipitation'] = intval($p['part p="n"'][0]['ppcp'][0]);
				$tableau[$index][1]['precipitation'] = NULL;
				$tableau[$index][1]['humidite'] = intval($p['part p="n"'][0]['hmid'][0]) ? intval($p['part p="n"'][0]['hmid'][0]) : '';

				$tableau[$index][1]['code_meteo'] = intval($p['part p="n"'][0]['icon'][0]) ? intval($p['part p="n"'][0]['icon'][0]) : '';
				$tableau[$index][1]['icon_meteo'] = NULL;
				$tableau[$index][1]['desc_meteo'] = NULL;

				$tableau[$index][1]['icone'] = $tableau[$index][1]['code_meteo'];
				$tableau[$index][1]['resume'] = $tableau[$index][1]['code_meteo'];

				// Détermination du mode jour/nuit
				$tableau[$index]['periode'] = (($index == 0) AND !$tableau[$index][0]['code_meteo']) ? 1 : 0;

				$index += 1;
			}
		}
	}

	// Traitement des erreurs de flux
	$tableau[$index]['erreur'] = (!$tableau) ? 'chargement' : '';

	// On stocke en fin de tableau la date de derniere mise a jour et le nombre max de  jours de prévisions
	$tableau[$index]['derniere_maj'] = date_format($date_maj,'Y-m-d H:i:s');
	$tableau[$index]['max_jours'] = _RAINETTE_WEATHER_JOURS_PREVISIONS;

	return $tableau;
}


function weather_flux2conditions($flux, $lieu) {
	$tableau = array();
	$n = spip_xml_match_nodes(",^cc,",$flux,$conditions);
	if ($n==1){
		$conditions = reset($conditions['cc']);
		// recuperer la date de derniere mise a jour des conditions
		if ($conditions) {
			// Date d'observation : elle est exprimée dans le fuseau horaire du lieu sous la forme 'date time AM/PM fuseau'
			$date_service = $conditions['lsup'][0];
			// Certaines abréviations de fuseau ne sont pas reconnues par date_create suivant la configuration du serveur
			// http. On supprime cette information avant le date_create qui produite un timezone erronné mais qui ne genera
			// pas le format ensuite.
			if (!$date_maj = date_create($date_service)) {
				$elements_date = explode(' ', $date_service);
				array_pop($elements_date);
				$date_service = implode(' ', $elements_date);
				$date_maj = date_create($date_service);
			}
			$tableau['derniere_maj'] = date_format($date_maj,'Y-m-d H:i:s');
			// station d'observation (peut etre differente de la ville)
			$tableau['station'] = $conditions['obst'][0];

			// Liste des conditions meteo
			$tableau['vitesse_vent'] = floatval($conditions['wind'][0]['s'][0]);
			$tableau['angle_vent'] = intval($conditions['wind'][0]['d'][0]);
			$tableau['direction_vent'] = $conditions['wind'][0]['t'][0];

			$tableau['temperature_reelle'] = floatval($conditions['tmp'][0]);
			$tableau['temperature_ressentie'] = floatval($conditions['flik'][0]);

			$tableau['humidite'] = intval($conditions['hmid'][0]);
			$tableau['point_rosee'] = intval($conditions['dewp'][0]);

			$tableau['pression'] = floatval($conditions['bar'][0]['r'][0]);
			$tableau['tendance_pression'] = $conditions['bar'][0]['d'][0];

			$tableau['visibilite'] = floatval($conditions['vis'][0]);

			$tableau['code_meteo'] = intval($conditions['icon'][0]);
			$tableau['icon_meteo'] = NULL;
			$tableau['desc_meteo'] = $conditions['t'][0];

			// A priori la période de nuit commence à 14h et se termine à 5h.
			// Cette données n'est pas utile pour les conditions de ce service, on la positionne à NULL : TODO
			$tableau['periode'] = NULL;

			// La traduction du resume dans la bonne langue est toujours faite par les fichiers de langue SPIP
			// car l'API ne permet pas de choisir la langue. On ne stocke donc que le code meteo
			$tableau['icone'] = $tableau['code_meteo'];
			$tableau['resume'] = $tableau['code_meteo'];
		}
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? 'chargement' : '';

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
		$tableau['region'] = NULL;

		$tableau['longitude'] = floatval($infos['lon'][0]);
		$tableau['latitude'] = floatval($infos['lat'][0]);

		$tableau['population'] = NULL;
		$tableau['max_previsions'] = _RAINETTE_WEATHER_JOURS_PREVISIONS;
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? 'chargement' : '';

	return $tableau;
}

function weather_service2credits() {

	$credits['titre'] = NULL;
	$credits['logo'] = NULL;
	$credits['lien'] = 'http://www.weather.com/';

	return $credits;
}

?>
