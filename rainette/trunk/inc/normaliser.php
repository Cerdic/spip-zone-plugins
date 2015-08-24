<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_SEPARATEUR_DONNEE_MULTIPLE'))
	define('_RAINETTE_SEPARATEUR_DONNEE_MULTIPLE', ', ');


$GLOBALS['rainette_config']['conditions'] = array(
	// Données d'observation
	'derniere_maj'			=> array('origine' => 'service', 'type_php' => 'date'),
	'station'				=> array('origine' => 'service', 'type_php' => 'string'),
	// Températures
	'temperature_reelle'	=> array('origine' => 'service', 'type_php' => 'float'),
	'temperature_ressentie'	=> array('origine' => 'service', 'type_php' => 'float'),
	// Données anémométriques
	'vitesse_vent'			=> array('origine' => 'service', 'type_php' => 'float'),
	'angle_vent'			=> array('origine' => 'service', 'type_php' => 'int'),
	'direction_vent'		=> array('origine' => 'service', 'type_php' => 'string'),
	// Données atmosphériques
	'precipitation'			=> array('origine' => 'service', 'type_php' => 'float'),
	'humidite'				=> array('origine' => 'service', 'type_php' => 'int'),
	'point_rosee'			=> array('origine' => 'service', 'type_php' => ''),
	'pression'				=> array('origine' => 'service', 'type_php' => 'float'),
	'tendance_pression'		=> array('origine' => 'service', 'type_php' => ''),
	'visibilite'			=> array('origine' => 'service', 'type_php' => 'float'),
	'indice_uv'				=> array('origine' => 'service', 'type_php' => 'int'),
	'risque_uv'				=> array('origine' => 'calcul', 'type_php' => 'string'),
	// Etats météorologiques natifs
	'code_meteo'			=> array('origine' => 'service', 'type_php' => 'int'),
	'icon_meteo'			=> array('origine' => 'service', 'type_php' => 'string'),
	'desc_meteo'			=> array('origine' => 'service', 'type_php' => 'string'),
	// Etats météorologiques calculés
	'icone'					=> array('origine' => 'calcul', 'type_php' => 'mixed'),
	'resume'				=> array('origine' => 'calcul', 'type_php' => 'mixed'),
	'periode'				=> array('origine' => 'calcul', 'type_php' => 'int'),
);

$GLOBALS['rainette_config']['previsions'] = array(
	// Données d'observation
	'index'					=> array('origine' => 'calcul', 'type_php' => 'int'),
	'date'					=> array('origine' => 'service', 'type_php' => 'date'),
	// Données astronomiques
	'lever_soleil'			=> array('origine' => 'service', 'type_php' => 'date'),
	'coucher_soleil'		=> array('origine' => 'service', 'type_php' => 'date'),
	// Températures
	'temperature_max'	=> array('origine' => 'service', 'type_php' => 'float'),
	'temperature_min'	=> array('origine' => 'service', 'type_php' => 'float'),
	// Données anémométriques
	'vitesse_vent'			=> array('origine' => 'service', 'type_php' => 'float'),
	'angle_vent'			=> array('origine' => 'service', 'type_php' => 'int'),
	'direction_vent'		=> array('origine' => 'service', 'type_php' => 'string'),
	// Données atmosphériques
	'risque_precipitation'	=> array('origine' => 'service', 'type_php' => 'int'),
	'precipitation'			=> array('origine' => 'service', 'type_php' => 'float'),
	'humidite'				=> array('origine' => 'service', 'type_php' => 'int'),
	'pression'				=> array('origine' => 'service', 'type_php' => 'float'),
	'indice_uv'				=> array('origine' => 'service', 'type_php' => 'int'),
	'risque_uv'				=> array('origine' => 'calcul', 'type_php' => 'string'),
	// Etats météorologiques natifs
	'code_meteo'			=> array('origine' => 'service', 'type_php' => 'int'),
	'icon_meteo'			=> array('origine' => 'service', 'type_php' => 'string'),
	'desc_meteo'			=> array('origine' => 'service', 'type_php' => 'string'),
	// Etats météorologiques calculés
	'icone'					=> array('origine' => 'calcul', 'type_php' => 'mixed'),
	'resume'				=> array('origine' => 'calcul', 'type_php' => 'mixed'),
	'periode'				=> array('origine' => 'calcul', 'type_php' => 'int'),
	// Informations complémentaires
	'max_jours'				=> array('origine' => 'calcul', 'type_php' => 'int'),
);

$GLOBALS['rainette_config']['infos'] = array(
	// Lieu
	'ville'					=> array('origine' => 'service', 'type_php' => 'string'),
	'region'				=> array('origine' => 'service', 'type_php' => 'string'),
	// Coordonnées
	'longitude'				=> array('origine' => 'service', 'type_php' => 'float'),
	'latitude'				=> array('origine' => 'service', 'type_php' => 'float'),
	// Données démographiques
	'population'			=> array('origine' => 'service', 'type_php' => 'int'),
	// Informations complémentaires
	'max_previsions'		=> array('origine' => 'calcul', 'type_php' => 'int'),
);


function get_donnees_service($config_service, $mode, $format, $systeme_unite, $flux) {
	global $rainette_config;
	$tableau = array();

	$donnees_service = get_element($flux, $config_service[$mode][$format]['base']);

	if ($donnees_service !== NULL) {
		// Le service a renvoyé des données, on boucle sur les clés du tableau normalisé
		foreach (array_keys($rainette_config[$mode]) as $_donnee) {
			if ($rainette_config[$mode][$_donnee]['origine'] == 'service') {
				// La donnée est fournie par le service ou n'est pas disponible. Elle n'est jamais calculée
				// par le plugin
				if ($cle_service = $config_service[$mode][$format]['donnees'][$_donnee]['cle']) {
					// La donnée est normalement fournie par le service car elle possède une configuration
					// de clé
					$cle_donnee = array();
					// On détermine en premier lieu si la donnée est composée d'une ou plusieurs (2)
					// valeurs du service. Dans ce dernier cas la clé est écrite 'cle1+cle2' et les données
					// sont toujours des chaines de caractères.
					if (strpos($cle_service[0], '+') !== false) {
						$cles_0 = explode('+', $cle_service[0]);
						array_shift($cle_service);
						foreach ($cles_0 as $_cle_0) {
							$cle_donnee[] = array_merge(array($_cle_0), $cle_service);
						}
					} else {
						// On traite le cas où le nom de la clé varie suivant le système d'unité choisi.
						// Ce n'est jamais le cas pour les données composées
						if ($config_service[$mode][$format]['donnees'][$_donnee]['suffixe_unite']) {
							$cle_service[0] .= $config_service[$mode][$format]['donnees'][$_donnee]['suffixe_unite'][$systeme_unite];
						}
						$cle_donnee[] = $cle_service;
					}

					// On utilise donc la première clé qui est souvent la seule (cas d'une composition est rare)
					// pour calculer la valeur du service.
					$valeur_service = '';
					foreach ($cle_donnee as $_cle_donnee) {
						$valeur = get_element($donnees_service, $_cle_donnee);
						if ($valeur !== NULL) {
							$typer = get_typage($mode, $_donnee);
							if ($rainette_config[$mode][$_donnee]['type_php'] == 'string') {
								$valeur_service .= $valeur_service
									? _RAINETTE_SEPARATEUR_DONNEE_MULTIPLE . $typer($valeur)
									: $typer($valeur);
							} else {
								$valeur_service = $typer($valeur);
								if ($rainette_config[$mode][$_donnee]['type_php'] == 'date') {
									$valeur_service = date('Y-m-d H:i:s', $valeur_service);
								}
							}
						}
					}
					// Si la valeur était disponible on la stocke sinon on met la donnée à chaine vide pour
					// montrer l'indisponibilité temporaire.
					$tableau[$_donnee] = $valeur_service;
				} else {
					// La données météo n'est jamais fournie par le service. On la positionne à NULL pour
					// la distinguer avec une donnée vide car indisponible temporairement.
					$tableau[$_donnee] = NULL;
				}
			} else {
				// La données météo est toujours calculée à posteriori par le plugin indépendamment
				// du service. On l'initialise temporairement à la chaine vide.
				$tableau[$_donnee] = '';
			}
		}
	}
	return $tableau;
}

function initialiser_tableau_standard($mode, $config_service=array()) {
	global $rainette_config;
	$tableau = array();

	$donnees = $rainette_config[$mode];
	if ($donnees) {
		foreach ($donnees as $_donnee => $_config) {
			$tableau[$_donnee] = '';
			if (($_config['origine'] == 'service')
			and (!$config_service[$_donnee]['cle'])) {
				$tableau[$_donnee] = NULL;
			}
		}
	}

	return $tableau;
}

function get_element($tableau, $indexes) {
    $erreur = false;
    $element = $tableau;
   	foreach ($indexes as $_index) {
   		if (isset($element[$_index])) {
            $element = $element[$_index];
   		}
   		else {
   			$erreur = true;
   			break;
   		}
   	}
    return ($erreur ? NULL : $element);
}

function get_configuration($mode, $donnee='', $information='') {
	global $rainette_config;

	if ($donnee) {
		if ($information) {
			$config = isset($rainette_config[$mode][$donnee][$information])
				? $rainette_config[$mode][$donnee][$information]
				: '';
		}
		else {
			$config = isset($rainette_config[$mode][$donnee])
				? $rainette_config[$mode][$donnee]
				: array();
		}
	}
	else {
		$config = isset($rainette_config[$mode])
			? $rainette_config[$mode]
			: array();
	}

	return $config;
}

function get_typage($mode, $donnee) {
	global $rainette_config;
	$fonction = '';

	$type_php = isset($rainette_config[$mode][$donnee]['type_php'])
		? $rainette_config[$mode][$donnee]['type_php']
		: '';
	if ($type_php) {
		switch ($type_php) {
			case 'float':
		        $fonction = 'floatval';
				break;
			case 'int':
				$fonction = 'intval';
				break;
			case 'string':
				$fonction = 'strval';
				break;
			case 'date':
				$fonction = 'strtotime';
				break;
			default:
				$fonction = '';
		}
	}

	return $fonction;
}


?>