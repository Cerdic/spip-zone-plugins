<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_DEBUG'))
	define ('_RAINETTE_DEBUG', false);
if (!defined('_RAINETTE_DONNEES_PREVISIONS'))
	define('_RAINETTE_DONNEES_PREVISIONS', '');
if (!defined('_RAINETTE_DONNEES_CONDITIONS'))
	define('_RAINETTE_DONNEES_CONDITIONS', 'derniere_maj:station:vitesse_vent:angle_vent:direction_vent:temperature_reelle:temperature_ressentie:
	humidite:point_rosee:pression:tendance_pression:visibilite:code_meteo:icon_meteo:desc_meteo:periode:icone:resume');
if (!defined('_RAINETTE_DONNEES_INFOS'))
	define('_RAINETTE_DONNEES_INFOS', 'ville:region:longitude:latitude:population');

$GLOBALS['cfg_conditions'] = array(
	'derniere_maj'			=> array(
								'type'		=> 'date',
								'type_php'	=> 'string',
								'groupe'	=> 'observation'),
	'station'				=> array(
								'type'		=> 'texte',
								'type_php'	=> 'string',
								'groupe'	=> 'observation'),
	'temperature_reelle'	=> array(
								'type'		=> 'temperature',
								'type_php'	=> 'float',
								'groupe'	=> 'temperature'),
	'temperature_ressentie'	=> array(
								'type'		=> 'temperature',
								'type_php'	=> 'float',
								'groupe'	=> 'temperature'),
	'vitesse_vent'			=> array(
								'type'		=> 'vitesse',
								'type_php'	=> 'float',
								'groupe'	=> 'anemometrie'),
	'angle_vent'			=> array(
								'type'		=> 'vitesse',
								'type_php'	=> 'int',
								'groupe'	=> 'anemometrie'),
	'direction_vent'		=> array(
								'type'		=> 'texte',
								'type_php'	=> 'string',
								'groupe'	=> 'anemometrie'),
	'humidite'				=> array(
								'type'		=> 'pourcentage',
								'type_php'	=> 'int',
								'groupe'	=> 'atmosphere'),
	'point_rosee'			=> array(
								'type'		=> 'temperature',
								'type_php'	=> 'int',
								'groupe'	=> 'atmosphere'),
	'pression'				=> array(
								'type'		=> 'pression',
								'type_php'	=> 'float',
								'groupe'	=> 'atmosphere'),
	'tendance_pression'		=> array(
								'type'		=> 'texte',
								'type_php'	=> 'string',
								'groupe'	=> 'atmosphere'),
	'visibilite'			=> array(
								'type'		=> 'distance',
								'type_php'	=> 'float',
								'groupe'	=> 'atmosphere'),
);


function rainette_debug_comparer($mode='previsions', $jeu=array()) {
	$debug = array();

	if (_RAINETTE_DEBUG) {
		if (!$mode)
			$mode = 'previsions';

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
							$debug[$_donnee][$_service] = $tableau[$_donnee];
						}
					}
				}
			}
		}
	}

	return $debug;
}

function rainette_debug($lieu, $mode='previsions', $service='weather') {
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


?>