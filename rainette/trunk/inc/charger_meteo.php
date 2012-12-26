<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define ('_RAINETTE_RELOAD_TIME_PREVISIONS',2*3600); // pas la peine de recharger des previsions de moins de 2h
define ('_RAINETTE_RELOAD_TIME_CONDITIONS',1800); // pas la peine de recharger les conditions courantes de moins de 30mn

/**
 * charger le fichier des infos meteos correspondant au code
 * si le fichier cache est trop vieux ou absent, on charge le xml et on l'analyse
 * puis on stocke les infos apres analyse
 *
 * @param string $lieu
 * @return string
 */
function inc_charger_meteo_dist($lieu, $mode='previsions', $service='weather') {

	// Traitement des cas ou les arguments sont vides
	if (!$mode) $mode = 'previsions';
	if (!$service) $service = 'weather';

	// En fonction du service, on inclut le fichier des fonctions
	// Le principe est que chaque service propose la même liste de fonctions d'interface dans un fichier unique
	include_spip("services/${service}");

	$cacher = "${service}_service2cache";
	$cache = $cacher($lieu, $mode);

	$reloader = "${service}_service2reload_time";
	$reload_time = ($mode == 'previsions') ? $reloader('previsions') : $reloader('conditions');

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

?>