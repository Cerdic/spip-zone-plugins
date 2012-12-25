<?php

define ('_RAINETTE_RELOAD_TIME_PREVISIONS',2*3600); // pas la peine de recharger des previsions de moins de 2h
define ('_RAINETTE_RELOAD_TIME_CONDITIONS',1800); // pas la peine de recharger les conditions courantes de moins de 30mn


function meteo2resume($meteo){

	// On utilise l'option de _T permettant de savoir si un item existe ou pas
	$resume = _T('rainette:meteo_' . $meteo, array(), array('force' => false));
	if (!$resume)
		$resume = _T('rainette:meteo_na'." ($meteo)");

	return ucfirst($resume);
}

function angle2direction($degre) {
	$dir = '';
	switch(round($degre / 22.5) % 16)
	{
		case 0:  $dir = 'N'; break;
		case 1:  $dir = 'NNE'; break;
		case 2:  $dir = 'NE'; break;
		case 3:  $dir = 'ENE'; break;
		case 4:  $dir = 'E'; break;
		case 5:  $dir = 'ESE'; break;
		case 6:  $dir = 'SE'; break;
		case 7:  $dir = 'SSE'; break;
		case 8:  $dir = 'S'; break;
		case 9:  $dir = 'SSW'; break;
		case 10: $dir = 'SW'; break;
		case 11: $dir = 'WSW'; break;
		case 12: $dir = 'W'; break;
		case 13: $dir = 'WNW'; break;
		case 14: $dir = 'NW'; break;
		case 15: $dir = 'NNW'; break;
	}
	return $dir;
}

/**
 * charger le fichier des infos meteos correspondant au code
 * si le fichier cache est trop vieux ou absent, on charge le xml et on l'analyse
 * puis on stocke les infos apres analyse
 *
 * @param string $lieu
 * @return string
 */
function charger_meteo($lieu, $mode='previsions', $service='weather') {

	// Traitement des cas ou les arguments sont vides
	if (!$mode) $mode = 'previsions';
	if (!$service) $service = 'weather';

	// En fonction du service, on inclut le fichier des fonctions
	// Le principe est que chaque service propose la même liste de fonctions d'interface dans un fichier unique
	include_spip("services/${service}");

	$cacher = "${service}_service2cache";
	$cache = $cacher($lieu, $mode);

	$reload_time = ($mode == 'previsions') ? _RAINETTE_RELOAD_TIME_PREVISIONS : _RAINETTE_RELOAD_TIME_CONDITIONS;

	if (!file_exists($cache)
	OR (($mode != 'infos') AND (!filemtime($cache) OR (time()-filemtime($cache)>$reload_time)))) {
		// Traitement du fichier d'infos
		$urler = "${service}_service2url";
		$url = $urler($lieu, $mode);

		$acquerir = "${service}_url2flux";
		$flux = $acquerir($url);

		if ($mode == 'infos')
			$convertir = "${service}_flux2infos";
		else
			$convertir = ($mode == 'previsions') ? "${service}_flux2previsions" : "${service}_flux2conditions";
		$tableau = $convertir($flux, $lieu);
		ecrire_fichier($cache, serialize($tableau));
	}

	return $cache;
}


function url2flux_xml($url) {

	include_spip('inc/distant');
	$flux = recuperer_page($url);

	$xml = @simplexml2array(simplexml_load_string($flux));

	return $xml;
}


function url2flux_json($url) {

	include_spip('inc/distant');
	$flux = recuperer_page($url);

	// TODO : à compléter avec le traitement JSON pour OpenWeatherMap

	return $xml;
}


/**
 * Transforme un objet SimpleXML en tableau PHP
 *
 * @param object $obj
 * @return array
**/
// http://www.php.net/manual/pt_BR/book.simplexml.php#108688
// xaviered at gmail dot com 17-May-2012 07:00
function simplexml2array($obj) {

	// Cette fonction getDocNamespaces() est longue sur de gros xml
	$namespace = $obj->getDocNamespaces(true);
	$namespace[NULL] = NULL;

	$children = array();
	$attributes = array();
	$name = strtolower((string)$obj->getName());

	$text = trim((string)$obj);
	if( strlen($text) <= 0 ) {
		$text = NULL;
	}

	// get info for all namespaces
	if (is_object($obj)) {
		foreach( $namespace as $ns=>$nsUrl ) {
			// atributes
			$objAttributes = $obj->attributes($ns, true);
			foreach( $objAttributes as $attributeName => $attributeValue ) {
				$attribName = strtolower(trim((string)$attributeName));
				$attribVal = trim((string)$attributeValue);
				if (!empty($ns)) {
					$attribName = $ns . ':' . $attribName;
				}
				$attributes[$attribName] = $attribVal;
			}

			// children
			$objChildren = $obj->children($ns, true);
			foreach( $objChildren as $childName=>$child ) {
				$childName = strtolower((string)$childName);
				if( !empty($ns) ) {
					$childName = $ns.':'.$childName;
				}
				$children[$childName][] = simplexml2array($child);
			}
		}
	}

	return array(
		'name'=>$name,
		'text'=>$text,
		'attributes'=>$attributes,
		'children'=>$children
	);
}

function kilometre2mile($km) {
	return 0.6215*$km;
}

function celsius2farenheit($c) {
	return $c*9/5 + 32;
}

function millimetre2inch($mm) {
	return $mm/25.4;
}

function millibar2inch($mbar) {
	return $mbar/33.86;
}

function temperature2ressenti($temperature, $vitesse_vent) {

	// La temperature ressentie n'est calculee que pour des temperatures ambiantes comprises entre
	// -50°C et +10°C
	if (($temperature >= -50) AND ($temperature <= 10)) {
		if ($vitesse_vent > 4.8)
			$ressenti = 13.12 + 0.6215*$temperature + (0.3965*$temperature - 11.37)*pow($vitesse_vent, 0.16);
		else
			$ressenti = $temperature + 0.2*(0.1345*$temperature - 1.59)*$vitesse_vent;
	}
	else
		$ressenti = $temperature;

	return $ressenti;
}

?>