<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Renvoyer le nom du fichier cache des données météos correspondant au lieu et au type de données choisis.
 *
 * Si le fichier cache est obsolète ou absent, on le crée après avoir chargé puis phrasé le flux XML ou JSON
 * et stocké les données collectées et transcodées dans un tableau standardisé.
 *
 * @param string $lieu
 * 		Le lieu concerné par la méteo exprimé selon les critères requis par le service
 * @param string $mode
 * 		Le type de données météorologiques demandé :
 * 			- 'previsions', la valeur par défaut
 * 			- 'conditions'
 * 			- 'infos'
 * @param string $service
 * 		Le nom abrégé du service :
 * 			- 'weather' pour le weather.com, la valeur par défaut
 * 			- 'wwo' pour World Weather Online
 * 			- 'wunderground' pour Wunderground
 * 			- 'yahoo' pour Yahoo! Weather
 * 			- 'owm' pour Open Weather Map
 *
 * @return string
 * 		Le nom du fichier cache correspondant à la demande.
 */
function inc_charger_meteo_dist($lieu, $mode='previsions', $service='weather') {

	// Traitement des cas ou les arguments sont vides
	if (!$mode) $mode = 'previsions';
	if (!$service) $service = 'weather';

	// En fonction du service, on inclut le fichier des fonctions
	// Le principe est que chaque service propose la même liste de fonctions d'interface dans un fichier unique
	include_spip("services/${service}");

	// Construire le nom du fichier cache
	$cacher = "${service}_service2cache";
	$cache = $cacher($lieu, $mode);

	// Déterminer la période de renouvellement du cache
	$reloader = "${service}_service2reload_time";
	$reload_time = ($mode == 'previsions') ? $reloader('previsions') : $reloader('conditions');

	// Déterminer le système d'unité utilisé dans le cache et celui requis par la config.
	// Si ces système d'unité diffèrent il faut renouveler le cache sinon on affichera des données
	// fausses avec une unité correcte et ce jusqu'à la prochaine échéance du cache.
	$unite_config = '';
	$unite_cache = '';
	if (file_exists($cache) AND ($mode != 'infos')) {
		include_spip('inc/config');
		$unite_config = lire_config("rainette/${service}/unite", 'm');

		lire_fichier($cache, $contenu);
		$tableau = unserialize($contenu);
		if ($mode == 'previsions') {
			$index = count($tableau)-1;
			$unite_cache = isset($tableau[$index]['config']['unite']) ? $tableau[$index]['config']['unite'] : 'm';
		} else {
			$unite_cache = isset($tableau['config']['unite']) ? $tableau['config']['unite'] : 'm';
		}
	}

	// Mise à jour du cache avec les nouvelles données météo si besoin
	if (!file_exists($cache)
	OR (($mode != 'infos') AND (!filemtime($cache) OR (time()-filemtime($cache)>$reload_time)))
	OR (($mode != 'infos') AND ($unite_config != $unite_cache))) {
		// Construire l'url de la requête
		$urler = "${service}_service2url";
		$url = $urler($lieu, $mode);

		// Acquérir le flux XML ou JSON dans un tableau
		$acquerir = "${service}_url2flux";
		$flux = $acquerir($url);

		// Convertir le flux en tableau standard pour la mise en cache
		if ($mode == 'infos')
			$convertir = "${service}_flux2infos";
		else
			$convertir = ($mode == 'previsions') ? "${service}_flux2previsions" : "${service}_flux2conditions";
		$tableau = $convertir($flux, $lieu);

		// Ajouter les crédits affichés en regard de chaque modèle et stocker la configuration du service
		$crediter = "${service}_service2credits";
		$config = lire_config("rainette/${service}", array());
		if ($mode == 'previsions') {
			// Pour les prévisions les informations communes sont stockées dans un index supplémentaire en fin de tableau
			$index = count($tableau)-1;
			$tableau[$index]['credits'] = $crediter();
			$tableau[$index]['config'] = $config;
		}
		else {
			$tableau['credits'] = $crediter();
			$tableau['config'] = $config;
		}

	    // Pipeline de fin de chargement des données météo. Peut-être utilisé :
		// -- pour effectuer des traitements annexes à partir des données météo (archivage, par exemple)
		// -- pour ajouter ou modifier des données au tableau (la modification n'est pas conseillée cependant)
		$tableau = pipeline('post_chargement_meteo',
			array(
				'args' => array('lieu' => $lieu, 'mode' => $mode, 'service' => $service),
				'data' => $tableau));

		// Création du nouveau cache
		ecrire_fichier($cache, serialize($tableau));
	}

	return $cache;
}

?>
