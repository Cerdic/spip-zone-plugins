<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_REGEXP_LIEU_IP')) {
	/**
	 * Regexp permettant de reconnaitre un lieu au format adresse IP
	 */
	define('_RAINETTE_REGEXP_LIEU_IP', '#(?:\d{1,3}\.){3}\d{1,3}#');
}
if (!defined('_RAINETTE_REGEXP_LIEU_COORDONNEES')) {
	/**
	 * Regexp permettant de reconnaitre un lieu au format coordonnées géographiques latitude,longitude
	 */
	define('_RAINETTE_REGEXP_LIEU_COORDONNEES', '#([\-\+]?\d+(?:\.\d+)?)\s*,\s*([\-\+]?\d+(?:\.\d+)?)#');
}
if (!defined('_RAINETTE_REGEXP_LIEU_WEATHER_ID')) {
	/**
	 * Regexp permettant de reconnaitre un lieu au format Weather ID
	 */
	define('_RAINETTE_REGEXP_LIEU_WEATHER_ID', '#[a-zA-Z]{4}\d{4}#i');
}

$GLOBALS['rainette_config']['infos'] = array(
	// Lieu
	'ville'          => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_lieu'),
	'pays'           => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_lieu'),
	'pays_iso2'      => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_lieu'),
	'region'         => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_lieu'),
	// Coordonnées géographiques
	'longitude'      => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'angle', 'groupe' => 'donnees_coordonnees'),
	'latitude'       => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'angle', 'groupe' => 'donnees_coordonnees'),
);

$GLOBALS['rainette_config']['conditions'] = array(
	// Données d'observation
	'derniere_maj'          => array('origine' => 'service', 'type_php' => 'date', 'type_unite' => '', 'groupe' => 'donnees_observation'),
	'station'               => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_observation'),
	// Températures
	'temperature_reelle'    => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'groupe' => 'donnees_temperatures'),
	'temperature_ressentie' => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'groupe' => 'donnees_temperatures'),
	// Données anémométriques
	'vitesse_vent'          => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'vitesse', 'groupe' => 'donnees_anemometriques'),
	'angle_vent'            => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'angle', 'groupe' => 'donnees_anemometriques'),
	'direction_vent'        => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_anemometriques'),
	// Données atmosphériques
	'precipitation'         => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'precipitation', 'groupe' => 'donnees_atmospheriques'),
	'humidite'              => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'pourcentage', 'groupe' => 'donnees_atmospheriques'),
	'point_rosee'           => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'temperature', 'groupe' => 'donnees_atmospheriques'),
	'pression'              => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'pression', 'groupe' => 'donnees_atmospheriques'),
	'tendance_pression'     => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_atmospheriques'),
	'visibilite'            => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'distance', 'groupe' => 'donnees_atmospheriques'),
	'indice_uv'             => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'indice', 'groupe' => 'donnees_atmospheriques'),
	'risque_uv'             => array('origine' => 'calcul', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_atmospheriques'),
	// Etats météorologiques natifs
	'code_meteo'            => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_etats_natifs'),
	'icon_meteo'            => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_etats_natifs'),
	'desc_meteo'            => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_etats_natifs'),
	// Etats météorologiques calculés
	'icone'                 => array('origine' => 'calcul', 'type_php' => 'mixed', 'type_unite' => '', 'groupe' => 'donnees_etats_calcules'),
	'resume'                => array('origine' => 'calcul', 'type_php' => 'mixed', 'type_unite' => '', 'groupe' => 'donnees_etats_calcules'),
	'periode'               => array('origine' => 'calcul', 'type_php' => 'int', 'type_unite' => '', 'groupe' => 'donnees_etats_calcules'),
);

$GLOBALS['rainette_config']['previsions'] = array(
	// Données d'observation
	'date'                 => array('origine' => 'service', 'type_php' => 'date', 'type_unite' => '', 'rangement' => 'jour', 'groupe' => 'donnees_observation'),
	'heure'                => array('origine' => 'service', 'type_php' => 'heure', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_observation'),
	// Données astronomiques
	'lever_soleil'         => array('origine' => 'service', 'type_php' => 'date', 'type_unite' => '', 'rangement' => 'jour', 'groupe' => 'donnees_astronomiques'),
	'coucher_soleil'       => array('origine' => 'service', 'type_php' => 'date', 'type_unite' => '', 'rangement' => 'jour', 'groupe' => 'donnees_astronomiques'),
	// Températures
	'temperature'          => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'rangement' => 'heure', 'groupe' => 'donnees_temperatures'),
	'temperature_max'      => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'rangement' => 'jour', 'groupe' => 'donnees_temperatures'),
	'temperature_min'      => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'rangement' => 'jour', 'groupe' => 'donnees_temperatures'),
	// Données anémométriques
	'vitesse_vent'         => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'vitesse', 'rangement' => 'heure', 'groupe' => 'donnees_anemometriques'),
	'angle_vent'           => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'angle', 'rangement' => 'heure', 'groupe' => 'donnees_anemometriques'),
	'direction_vent'       => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_anemometriques'),
	// Données atmosphériques
	'risque_precipitation' => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'pourcentage', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'precipitation'        => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'precipitation', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'humidite'             => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'pourcentage', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'point_rosee'          => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'temperature', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'pression'             => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'pression', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'visibilite'           => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'distance', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'indice_uv'            => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'indice', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'risque_uv'            => array('origine' => 'calcul', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	// Etats météorologiques natifs
	'code_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_natifs'),
	'icon_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_natifs'),
	'desc_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_natifs'),
	// Etats météorologiques calculés
	'icone'                => array('origine' => 'calcul', 'type_php' => 'mixed', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_calcules'),
	'resume'               => array('origine' => 'calcul', 'type_php' => 'mixed', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_calcules'),
	'periode'              => array('origine' => 'calcul', 'type_php' => 'int', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_calcules'),
);

$GLOBALS['rainette_config']['periodicite'] = array(
	24 => array(24, 12),
	12 => array(12),
	1  => array(1,3,6)
);



/**
 * @param $config_service
 * @param $mode
 * @param $flux
 * @param $periode
 *
 * @return array
 */
function service2donnees($config_service, $mode, $flux, $periode) {
	$tableau = array();

	include_spip('inc/filtres');
	if ($flux !== null) {
		// Le service a renvoyé des données, on boucle sur les clés du tableau normalisé
		// Néanmoins, en fonction de la période fournie en argument on filtre les données uniquement
		// utiles à cette période.
		foreach (array_keys($GLOBALS['rainette_config'][$mode]) as $_donnee) {
			if ((($periode == -1)
				and (empty($GLOBALS['rainette_config'][$mode][$_donnee]['rangement'])
					or ($GLOBALS['rainette_config'][$mode][$_donnee]['rangement'] == 'jour')))
			or (($periode > -1)	and ($GLOBALS['rainette_config'][$mode][$_donnee]['rangement'] == 'heure'))) {
				if ($GLOBALS['rainette_config'][$mode][$_donnee]['origine'] == 'service') {
					// La donnée est fournie par le service. Elle n'est jamais calculée par le plugin
					// Néanmoins, elle peut-être indisponible temporairement
					if ($cle_service = $config_service['donnees'][$_donnee]['cle']) {
						// La donnée est normalement fournie par le service car elle possède une configuration de clé
						// On traite le cas où le nom de la clé varie suivant le système d'unité choisi.
						// La clé de base peut être vide, le suffixe contenant dès lors toute la clé.
						if (!empty($config_service['donnees'][$_donnee]['suffixe'])) {
							$systeme_unite = $config_service['unite'];
							$id_suffixee = $config_service['donnees'][$_donnee]['suffixe']['id_cle'];
							$cle_service[$id_suffixee] .= $config_service['donnees'][$_donnee]['suffixe'][$systeme_unite];
						}

						// On utilise donc la clé pour calculer la valeur du service.
						// Si la valeur est disponible on la stocke sinon on met la donnée à chaine vide pour
						// montrer l'indisponibilité temporaire.
						$donnee = '';
						$valeur_service = empty($cle_service)
							? $flux
							: table_valeur($flux, implode('/', $cle_service), '');
						if ($valeur_service !== '') {
							$typer = donnee2typage($mode, $_donnee);
							$donnee = $typer($valeur_service);
						}
					} else {
						// La donnée météo n'est jamais fournie par le service. On la positionne à null pour
						// la distinguer avec une donnée vide car indisponible temporairement.
						$donnee = null;
					}
				} else {
					// La données météo est toujours calculée à posteriori par le plugin indépendamment
					// du service. On l'initialise temporairement à la chaine vide.
					$donnee = '';
				}

				$tableau[$_donnee] = $donnee;
			}
		}
	}

	return $tableau;
}


/**
 * @param $mode
 * @param $donnee
 *
 * @return string
 */
function donnee2typage($mode, $donnee) {
	$fonction = '';

	$type_php = isset($GLOBALS['rainette_config'][$mode][$donnee]['type_php'])
		? $GLOBALS['rainette_config'][$mode][$donnee]['type_php']
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
				$fonction = 'donnee2date';
				break;
			case 'heure':
				$fonction = 'donnee2heure';
				break;
			default:
				$fonction = '';
		}
	}

	return $fonction;
}


/**
 * @param $donnee
 *
 * @return string
 */
function donnee2date($donnee) {
	if (is_numeric($donnee)) {
		$date = date('Y-m-d H:i:s', $donnee);
	} else {
		$date = date_create($donnee);
		if (!$date) {
			$elements_date = explode(' ', $donnee);
			array_pop($elements_date);
			$donnee = implode(' ', $elements_date);
			$date = date_create($donnee);
		}
		$date = date_format($date, 'Y-m-d H:i:s');
	}

	return $date;
}

/**
 * @param $donnee
 *
 * @return string
 */
function donnee2heure($donnee) {
	if (is_numeric($donnee)) {
		$taille = strlen($donnee);
		if ($taille < 3) {
			$m = '00';
			$h = $donnee;
		} else {
			$m = substr($donnee, -2);
			$h = strlen($donnee) == 3
				? substr($donnee, 0, 1)
				: substr($donnee, 0, 2);
		}
		$heure = "${h}:${m}";
	} else {
		$heure = $donnee;
	}

	return $heure;
}

function trouver_periodicite($type_modele, $service) {

	// Périodicité initialisée à "non trouvée"
	$periodicite = 0;

	if (isset($GLOBALS['rainette_config']['periodicite'][$type_modele])) {
		// Acquérir la configuration statique du service pour connaitre les périodicités horaires supportées
		// pour le mode prévisions
		include_spip("services/${service}");
		$configurer = "${service}_service2configuration";
		$configuration = $configurer('previsions');
		$periodicites_service = array_keys($configuration['periodicites']);

		$periodicites_modele = $GLOBALS['rainette_config']['periodicite'][$type_modele];
		foreach ($periodicites_modele as $_periodicite_modele) {
			if (in_array($_periodicite_modele, $periodicites_service)) {
				$periodicite = $_periodicite_modele;
				break;
			}
		}
	}

	return $periodicite;
}


function periodicite_compatible($type_modele, $periodicite) {

	// Périodicité initialisée à "non trouvée"
	$compatible = false;

	if (isset($GLOBALS['rainette_config']['periodicite'][$type_modele])
	and in_array($periodicite, $GLOBALS['rainette_config']['periodicite'][$type_modele])) {
		$compatible = true;
	}

	return $compatible;
}


function normaliser_lieu($lieu) {

	$lieu_normalise = trim($lieu);

	if (preg_match(_RAINETTE_REGEXP_LIEU_WEATHER_ID, $lieu_normalise, $match)) {
		$format_lieu = 'weather_id';
		$lieu_normalise = $match[0];
	} elseif (preg_match(_RAINETTE_REGEXP_LIEU_COORDONNEES, $lieu_normalise, $match)) {
		$format_lieu = 'latitude_longitude';
		$lieu_normalise = "{$match[1]},{$match[2]}";
	} elseif (preg_match(_RAINETTE_REGEXP_LIEU_IP, $lieu_normalise, $match)) {
		$format_lieu = 'adresse_ip';
		$lieu_normalise = $match[0];
	} else {
		$format_lieu = 'ville_pays';
		// On détermine la ville et éventuellement le pays (ville[,pays])
		// et on élimine les espaces par un seul +
		$elements = explode(',', $lieu_normalise);
		$lieu_normalise = trim($elements[0]) . (!empty($elements[1]) ? ',' . trim($elements[1]) : '');
		$lieu_normalise = preg_replace('#\s{1,}#', '+', $lieu_normalise);
	}

	return array($lieu_normalise, $format_lieu);
}


function normaliser_cache($service, $lieu, $mode, $periodicite, $code_langue) {

	// Création et/ou détermination du dossier de destination du cache en fonction du service
	$dossier_cache = sous_repertoire(_DIR_CACHE, 'rainette');
	$dossier_cache = sous_repertoire($dossier_cache, $service);

	// Le nom du fichier cache est composé comme suit, chaque élement étant séparé par un underscore :
	// -- le nom du lieu normalisé (sans espace et dont tous les caractères non alphanumériques sont remplacés par un tiret
	// -- le nom du mode (infos, conditions ou previsions) accolé à la périodicité du cache pour les prévisions uniquement
	// -- la langue du résumé si il existe ou rien si aucune traduction n'est fournie par le service
	list($lieu_normalise,) = normaliser_lieu($lieu);
	$fichier_cache = $dossier_cache
					 . str_replace(array(' ', ',', '+', '.', '/'), '-', $lieu_normalise)
					 . '_' . $mode
					 . ($periodicite ? strval($periodicite) : '')
					 . ($code_langue ? '_' . strtolower($code_langue) : '')
					 . '.txt';

	return $fichier_cache;
}