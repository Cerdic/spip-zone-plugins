<?php
function code2icone($icon) {
	$r = "na";
	if (($icon >= 1) && ($icon < 48)) $r = strval($icon);
	return $r;
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
 * si le fichier analyse est trop vieux ou absent, on charge le xml et on l'analyse
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
	$f = $cacher($lieu, $mode);

	if ($mode == 'infos') {
		// Traitement du fichier d'infos
		if (!file_exists($f)) {
			$urler = "${service}_service2url";
			$url = $urler($lieu, $mode);

			$acquerir = "${service}_url2flux";
			$flux = $acquerir($url);

			$convertir = "${service}_xml2infos";
			$tableau = $convertir($flux, $lieu);
			ecrire_fichier($f, serialize($tableau));
		}
	}
	else {
		// Traitement du fichier de donnees requis
		$reload_time = ($mode == 'previsions') ? _RAINETTE_RELOAD_TIME_PREVISIONS : _RAINETTE_RELOAD_TIME_CONDITIONS;
		if (!file_exists($f)
		  || !filemtime($f)
		  || (time()-filemtime($f)>$reload_time)) {
			$urler = "${service}_service2url";
			$flux = $urler($lieu, $mode);

			$acquerir = "${service}_url2flux";
			$flux = $acquerir($url);

			$convertir = ($mode == 'previsions') ? "${service}_xml2previsions" : "${service}_xml2conditions";
			$tableau = $convertir($xml, $lieu);
			ecrire_fichier($f, serialize($tableau));
		}
	}
	return $f;
}

function charger_infos($lieu='', $type_infos='', $service='weather') {

	// Traitement des cas ou les arguments sont vides
	if (!$lieu) return '';
	if (!$service) $service = 'weather';

	$nom_fichier = charger_meteo($lieu, 'infos', $service);
	lire_fichier($nom_fichier,$tableau);
	if (!$type_infos)
		return $tableau;
	else {
		$tableau = unserialize($tableau);
		$info = $tableau[strtolower($type_infos)];
		if (!$info) $info = ucfirst($type_infos) . "(" . $lieu . ")";
		return $info;
	}
}

?>