<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Charger le fichier des données météos correspondant au lieu et au mode choisi.
 * Si le fichier cache est obsolète ou absent, on charge le flux,  on l'analyse
 * et on stocke les données collectées apres transcodage en cache.
 *
 * @param string $lieu
 * @param string $mode
 * @param string $service
 *
 * @return string
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

	// Déterminer la période de renouvèlement du cache
	$reloader = "${service}_service2reload_time";
	$reload_time = ($mode == 'previsions') ? $reloader('previsions') : $reloader('conditions');

	if (!file_exists($cache)
	OR (($mode != 'infos') AND (!filemtime($cache) OR (time()-filemtime($cache)>$reload_time)))) {
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

		// Ajout du crédit affiché en regard de chaque modèle
		$crediter = "${service}_service2credits";
		$tableau['credits'] = $crediter();

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