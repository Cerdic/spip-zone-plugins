<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_DEBUG')) {
	define('_RAINETTE_DEBUG', false);
}

if (!defined('_RAINETTE_DEBUG_PREVISIONS')) {
	define('_RAINETTE_DEBUG_PREVISIONS',
		'Donn&#233;es d\'observation:date/heure
		|Donn&#233;es astronomiques:lever_soleil/coucher_soleil
		|Temp&#233;ratures:temperature:temperature_max:temperature_min
		|Donn&#233;es an&#233;mom&#233;triques:vitesse_vent/angle_vent/direction_vent
		|Donn&#233;es atmosph&#233;riques:risque_precipitation/precipitation/humidite/point_rosee/pression/visibilite/indice_uv/risque_uv
		|&#201;tats m&#233;t&#233orologiques natifs:code_meteo/icon_meteo/desc_meteo
		|&#201;tats m&#233;t&#233orologiques calcul&#233;s:periode/icone/resume');
}
if (!defined('_RAINETTE_DEBUG_CONDITIONS')) {
	define('_RAINETTE_DEBUG_CONDITIONS',
		'Donn&#233;es d\'observation:derniere_maj/station
		|Temp&#233;ratures:temperature_reelle/temperature_ressentie
		|Donn&#233;es an&#233;mom&#233;triques:vitesse_vent/angle_vent/direction_vent
		|Donn&#233;es atmosph&#233;riques:precipitation/humidite/point_rosee/pression/tendance_pression/visibilite/indice_uv/risque_uv
		|&#201;tats m&#233;t&#233orologiques natifs:code_meteo/icon_meteo/desc_meteo
		|&#201;tats m&#233;t&#233orologiques calcul&#233;s:icone/resume/periode');
}
if (!defined('_RAINETTE_DEBUG_INFOS')) {
	define('_RAINETTE_DEBUG_INFOS',
		'Lieu:ville/pays/pays_iso/region
		|Coordonn&#233;es:longitude/latitude');
}
if (!defined('_RAINETTE_DEBUG_TYPE_UNITE')) {
	define('_RAINETTE_DEBUG_TYPE_UNITE',
		'temperature:temperature_reelle,temperature_ressentie,point_rosee,temperature,temperature_max,temperature_min
		|vitesse:vitesse_vent
		|angle:angle_vent,longitude,latitude
		|pourcentage:risque_precipitation,humidite
		|pression:pression
		|distance:visibilite
		|precipitation:precipitation
		|indice:indice_uv');
}


function rainette_dbg_afficher_cache($lieu, $mode = 'previsions', $periodicite = 0, $service = 'weather') {
	$debug = '';

	// Recuperation du tableau des conditions courantes
	if (_RAINETTE_DEBUG and function_exists('bel_env')) {
		$charger = charger_fonction('charger_meteo', 'inc');
		$nom_fichier = $charger($lieu, $mode, $periodicite, $service);
		if ($nom_fichier) {
			$contenu = '';
			lire_fichier($nom_fichier, $contenu);
			$tableau = unserialize($contenu);

			$debug = bel_env(serialize($tableau));
		}
	}

	return $debug;
}


function rainette_dbg_comparer_services($mode = 'conditions', $jeu = array(), $periodicite = 0) {
	$debug = array();

	if (!$mode) {
		$mode = 'conditions';
	}

	$donnees = constant('_RAINETTE_DEBUG_' . strtoupper($mode));
	$donnees = explode(':', $donnees);

	if ($donnees) {
		if (!$jeu) {
			$jeu = array(
				'weather'      => 'FRXX0076',
				'owm'          => 'Paris,Fr',
				'wwo'          => 'Paris,France',
				'wunderground' => 'Paris,France'
			);
		}

		// Recuperation du tableau des conditions courantes
		foreach ($jeu as $_service => $_lieu) {
			$charger = charger_fonction('charger_meteo', 'inc');
			$nom_cache = $charger($_lieu, $mode, $periodicite, $_service);
			lire_fichier($nom_cache, $contenu_cache);
			$tableau = unserialize($contenu_cache);

			if (!$tableau['extras']['erreur']) {
				$tableau =$tableau['donnees'];
				foreach ($tableau as $_donnee => $_valeur) {
					$type = gettype($tableau[$_donnee]);
					$debug[$_donnee][$_service]['valeur'] = $_valeur;
					$debug[$_donnee][$_service]['type'] = $type;
					if ($_donnee != 'erreur') {
						$debug[$_donnee][$_service]['erreur'] = ($type === 'NULL') ? 'nonapi' : ($_valeur === '' ? 'erreur' : '');
					} else {
						$debug[$_donnee][$_service]['erreur'] = $_valeur ? 'erreur' : '';
					}
				}
			}
		}
	}

	return $debug;
}


function rainette_dbg_afficher_donnee($donnee, $valeur, $type_php, $service = 'weather') {
	static $types_unite = array();
	$texte = '';

	if (!$types_unite) {
		$config_types = explode('|', _RAINETTE_DEBUG_TYPE_UNITE);
		foreach ($config_types as $_config_type) {
			list($type, $donnees) = explode(':', trim($_config_type));
			foreach (explode(',', $donnees) as $_donnee) {
				$types_unite[$_donnee] = $type;
			}
		}
	}
	$type_donnee = isset($types_unite[$donnee]) ? $types_unite[$donnee] : '';

	if ($type_php === 'NULL') {
		$texte = '<del>API</del>';
		$type_php = '';
	} elseif ($valeur === '') {
		if ($donnee != 'erreur') {
			$texte = 'Indisponible';
		} else {
			$texte = $valeur ? $valeur : 'Ok';
		}
	} elseif ($type_php === 'array') {
		foreach ($valeur as $_cle => $_valeur) {
			$texte .= ($texte ? '<br />' : '') . "<strong>${_cle}</strong> : " . ((gettype($_valeur) === null) ? '<del>API</del>' : $_valeur);
		}
	} else {
		$texte = $type_donnee ? rainette_afficher_unite($valeur, $type_donnee, -1, $service) : $valeur;
	}
	$texte .= "<br /><em>${type_php}</em>";

	return $texte;
}
