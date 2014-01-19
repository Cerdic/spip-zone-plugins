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
								'type'		=> '',
								'type_php'	=> 'string',
								'groupe'	=> 'observation'),
	'station'				=> array(
								'type'		=> '',
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
								'type'		=> '',
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
								'type'		=> '',
								'type_php'	=> 'string',
								'groupe'	=> 'atmosphere'),
	'visibilite'			=> array(
								'type'		=> 'distance',
								'type_php'	=> 'float',
								'groupe'	=> 'atmosphere'),
);


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

	if (_RAINETTE_DEBUG) {
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
							$debug[$_donnee][$_service]['valeur'] = $tableau[$_donnee];
							$type = gettype($tableau[$_donnee]);
							$debug[$_donnee][$_service]['type'] = $type;
							if ($_donnee != 'erreur')
								$debug[$_donnee][$_service]['erreur'] = ($type === 'NULL') ? 'nonapi' : ($tableau[$_donnee] === '' ? 'erreur' : '');
							else
								$debug[$_donnee][$_service]['erreur'] = $tableau[$_donnee] ? 'erreur' : '';
						}
					}
				}
			}
		}
	}

	return $debug;
}


function rainette_dbg_afficher_donnee($donnee, $valeur, $type_php) {
	$texte = '';

	$type_donnee = $GLOBALS['cfg_conditions'][$donnee]['type'];
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
	else {
		$texte = $type_donnee ? rainette_afficher_unite($valeur, $type_donnee) : $valeur;
	}
	$texte .= "<br /><em>${type_php}</em>";

	return $texte;
}

?>