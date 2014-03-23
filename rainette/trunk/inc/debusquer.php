<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_DEBUG'))
	define ('_RAINETTE_DEBUG', false);

if (!defined('_RAINETTE_DONNEES_PREVISIONS'))
	define('_RAINETTE_DONNEES_PREVISIONS', 'index:date:lever_soleil:coucher_soleil:vitesse_vent:angle_vent:direction_vent:temperature_max:temperature_min:
		risque_precipitation:precipitation:humidite:pression:code_meteo:icon_meteo:desc_meteo:periode:icone:resume');
if (!defined('_RAINETTE_DONNEES_CONDITIONS'))
	define('_RAINETTE_DONNEES_CONDITIONS',
		'Donn&#233;es d\'observation:derniere_maj/station
		|Temp&#233;ratures:temperature_reelle/temperature_ressentie
		|Donn&#233;es an&#233;mom&#233;triques:vitesse_vent/angle_vent/direction_vent
		|Donn&#233;es atmosph&#233;riques:humidite/point_rosee/pression/tendance_pression/visibilite
		|&#201;tats m&#233;t&#233orologiques natifs:code_meteo/icon_meteo/desc_meteo
		|&#201;tats m&#233;t&#233orologiques calcul&#233;s:icone/resume/periode');
if (!defined('_RAINETTE_DONNEES_INFOS'))
	define('_RAINETTE_DONNEES_INFOS',
		'Lieu:ville/region
		|Coordonn&#233;es:longitude/latitude
		|D&#233;mographie:population');
if (!defined('_RAINETTE_DONNEES_TYPE_UNITE'))
	define('_RAINETTE_DONNEES_TYPE_UNITE',
		'population:population
		|temperature:temperature_reelle,temperature_ressentie,point_rosee,temperature_max,temperature_min
		|vitesse:vitesse_vent
		|angle:angle_vent,longitude,latitude
		|pourcentage:risque_precipitation,humidite
		|pression:pression
		|distance:visibilite
		|precipitation:precipitation');


function rainette_dbg_afficher_cache($lieu, $mode='previsions', $service='weather') {
	$debug = '';

	// Recuperation du tableau des conditions courantes
	if (_RAINETTE_DEBUG AND function_exists('bel_env')) {
		$charger = charger_fonction('charger_meteo', 'inc');
		$nom_fichier = $charger($lieu, $mode, $service);
		if ($nom_fichier) {
			lire_fichier($nom_fichier,$tableau);
			$tableau = unserialize($tableau);

			// On ajoute le lieu, le mode et le service au contexte fourni au modele
			if ($mode == 'previsions') {
				// Pour les prévisions les informations communes sont stockées dans un index supplémentaire en fin de tableau
				$index = count($tableau)-1;
				$tableau[$index]['lieu'] = $lieu;
				$tableau[$index]['mode'] = $mode;
				$tableau[$index]['service'] = $service;
			}
			else {
				$tableau['lieu'] = $lieu;
				$tableau['mode'] = $mode;
				$tableau['service'] = $service;
			}

			$debug = bel_env(serialize($tableau));
		}
	}

	return $debug;
}


function rainette_dbg_comparer_services($mode='conditions', $jeu=array()) {
	$debug = array();

	if (!$mode)
		$mode = 'conditions';

	$donnees = constant('_RAINETTE_DONNEES_' . strtoupper($mode));
	$donnees = explode(':', $donnees);

	if ($donnees) {
		if (!$jeu)
			$jeu = array(
				'weather' => 'FRXX0076',
				'owm' => 'Paris,Fr',
				'wwo' => 'Paris,France',
				'wunderground' => 'Paris,France',
				'yahoo' => '615702');

		// Recuperation du tableau des conditions courantes
		foreach($jeu as $_service => $_lieu) {
			$charger = charger_fonction('charger_meteo', 'inc');
			$nom_fichier = $charger($_lieu, $mode, $_service);
			if ($nom_fichier) {
				lire_fichier($nom_fichier,$tableau);
				$tableau = unserialize($tableau);
				if ($tableau) {
					foreach($tableau as $_donnee => $_valeur) {
						$type = gettype($tableau[$_donnee]);
						$debug[$_donnee][$_service]['valeur'] = $_valeur;
						$debug[$_donnee][$_service]['type'] = $type;
						if ($_donnee != 'erreur')
							$debug[$_donnee][$_service]['erreur'] = ($type === 'NULL') ? 'nonapi' : ($_valeur === '' ? 'erreur' : '');
						else
							$debug[$_donnee][$_service]['erreur'] = $_valeur ? 'erreur' : '';
					}
				}
			}
		}
	}

	return $debug;
}


function rainette_dbg_afficher_donnee($donnee, $valeur, $type_php) {
	static $types_unite = array();
	$texte = '';

	if (!$types_unite) {
		$config_types = explode('|', _RAINETTE_DONNEES_TYPE_UNITE);
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
	}
	elseif ($valeur === '') {
		if ($donnee != 'erreur')
			$texte =  'Indisponible';
		else {
			$texte = $valeur ? $valeur : 'Ok';
		}
	}
	elseif ($type_php === 'array') {
		foreach ($valeur as $_cle => $_valeur) {
			$texte .= ($texte ? '<br />' : '') . "<strong>${_cle}</strong> : " . ((gettype($_valeur) === NULL) ? '<del>API</del>' : $_valeur);
		}
	}
	else {
			$texte = $type_donnee ? rainette_afficher_unite($valeur, $type_donnee) : $valeur;
	}
	$texte .= "<br /><em>${type_php}</em>";

	return $texte;
}

?>