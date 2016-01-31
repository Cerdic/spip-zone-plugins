<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS['rainette_config']['infos'] = array(
	// Lieu
	'ville'          => array('origine' => 'service', 'type_php' => 'string'),
	'pays'           => array('origine' => 'service', 'type_php' => 'string'),
	'pays_iso2'      => array('origine' => 'service', 'type_php' => 'string'),
	'region'         => array('origine' => 'service', 'type_php' => 'string'),
	// Coordonnées
	'longitude'      => array('origine' => 'service', 'type_php' => 'float'),
	'latitude'       => array('origine' => 'service', 'type_php' => 'float'),
	// Informations complémentaires
	'max_previsions' => array('origine' => 'calcul', 'type_php' => 'int'),
);

$GLOBALS['rainette_config']['conditions'] = array(
	// Données d'observation
	'derniere_maj'          => array('origine' => 'service', 'type_php' => 'date'),
	'station'               => array('origine' => 'service', 'type_php' => 'string'),
	// Températures
	'temperature_reelle'    => array('origine' => 'service', 'type_php' => 'float'),
	'temperature_ressentie' => array('origine' => 'service', 'type_php' => 'float'),
	// Données anémométriques
	'vitesse_vent'          => array('origine' => 'service', 'type_php' => 'float'),
	'angle_vent'            => array('origine' => 'service', 'type_php' => 'int'),
	'direction_vent'        => array('origine' => 'service', 'type_php' => 'string'),
	// Données atmosphériques
	'precipitation'         => array('origine' => 'service', 'type_php' => 'float'),
	'humidite'              => array('origine' => 'service', 'type_php' => 'int'),
	'point_rosee'           => array('origine' => 'service', 'type_php' => 'int'),
	'pression'              => array('origine' => 'service', 'type_php' => 'float'),
	'tendance_pression'     => array('origine' => 'service', 'type_php' => 'string'),
	'visibilite'            => array('origine' => 'service', 'type_php' => 'float'),
	'indice_uv'             => array('origine' => 'service', 'type_php' => 'int'),
	'risque_uv'             => array('origine' => 'calcul', 'type_php' => 'string'),
	// Etats météorologiques natifs
	'code_meteo'            => array('origine' => 'service', 'type_php' => 'string'),
	'icon_meteo'            => array('origine' => 'service', 'type_php' => 'string'),
	'desc_meteo'            => array('origine' => 'service', 'type_php' => 'string'),
	// Etats météorologiques calculés
	'resume'                => array('origine' => 'calcul', 'type_php' => 'mixed'),
	'icone'                 => array('origine' => 'calcul', 'type_php' => 'mixed'),
	'periode'               => array('origine' => 'calcul', 'type_php' => 'int'),
);

$GLOBALS['rainette_config']['previsions'] = array(
	// Données d'observation
	'date'                 => array('origine' => 'service', 'type_php' => 'date', 'rangement' => 'jour'),
	'heure'                => array('origine' => 'service', 'type_php' => 'heure', 'rangement' => 'heure'),
	// Données astronomiques
	'lever_soleil'         => array('origine' => 'service', 'type_php' => 'date', 'rangement' => 'jour'),
	'coucher_soleil'       => array('origine' => 'service', 'type_php' => 'date', 'rangement' => 'jour'),
	// Températures
	'temperature'          => array('origine' => 'service', 'type_php' => 'float', 'rangement' => 'heure'),
	'temperature_max'      => array('origine' => 'service', 'type_php' => 'float', 'rangement' => 'jour'),
	'temperature_min'      => array('origine' => 'service', 'type_php' => 'float', 'rangement' => 'jour'),
	// Données anémométriques
	'vitesse_vent'         => array('origine' => 'service', 'type_php' => 'float', 'rangement' => 'heure'),
	'angle_vent'           => array('origine' => 'service', 'type_php' => 'int', 'rangement' => 'heure'),
	'direction_vent'       => array('origine' => 'service', 'type_php' => 'string', 'rangement' => 'heure'),
	// Données atmosphériques
	'risque_precipitation' => array('origine' => 'service', 'type_php' => 'int', 'rangement' => 'heure'),
	'precipitation'        => array('origine' => 'service', 'type_php' => 'float', 'rangement' => 'heure'),
	'humidite'             => array('origine' => 'service', 'type_php' => 'int', 'rangement' => 'heure'),
	'point_rosee'          => array('origine' => 'service', 'type_php' => 'int', 'rangement' => 'heure'),
	'pression'             => array('origine' => 'service', 'type_php' => 'float', 'rangement' => 'heure'),
	'visibilite'           => array('origine' => 'service', 'type_php' => 'float', 'rangement' => 'heure'),
	'indice_uv'            => array('origine' => 'service', 'type_php' => 'int', 'rangement' => 'heure'),
	'risque_uv'            => array('origine' => 'calcul', 'type_php' => 'string', 'rangement' => 'heure'),
	// Etats météorologiques natifs
	'code_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'rangement' => 'heure'),
	'icon_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'rangement' => 'heure'),
	'desc_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'rangement' => 'heure'),
	// Etats météorologiques calculés
	'icone'                => array('origine' => 'calcul', 'type_php' => 'mixed', 'rangement' => 'heure'),
	'resume'               => array('origine' => 'calcul', 'type_php' => 'mixed', 'rangement' => 'heure'),
	'periode'              => array('origine' => 'calcul', 'type_php' => 'int', 'rangement' => 'heure'),
	// Informations complémentaires
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
		$periodicites_service = array_keys($configuration['previsions']['periodicites']);

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

