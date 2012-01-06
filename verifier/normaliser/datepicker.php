<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Convertir une date vers un type de format
 */
function normaliser_datepicker_dist($valeur, $options=array(), &$erreur) {

	if (!isset($options['format'])) {
		$erreur = "Pas de format de date transmis pour normalisation.";
		return;
	}

	$format = $options['format'];
	$normaliser = charger_fonction('datepicker_'.$format, 'normaliser', true);

	if (!$normaliser) {
		$erreur = "Pas de normalisation trouvee pour 'date' vers '$format'";
		return;
	}

	return $normaliser($valeur, $options, $erreur);
}


/**
 * Convertir une date en datetime 
 *
**/
function normaliser_datepicker_datetime_dist($valeur, $options, &$erreur) {
	$defaut = '0000-00-00 00:00:00';

	if (!$valeur) {
		return $defaut;
	}

	$date = $valeur;
	if (isset($options['heure'])) {
		$date .= (' ' . $options['heure'] . ':00');
	} else {
		$date .= ' 00:00:00';
	}

	include_spip('inc/filtres');
	if (!$date = recup_date($date)) {
		$erreur = "Impossible d'extraire la date de $date";
		return;
	}

	if (!($date = mktime($date[3], $date[4], 0, (int)$date[1], (int)$date[2], (int)$date[0]))) {
		// mauvais format de date
		$erreur = "Impossible de normaliser la date...";
		return false;
	}

	$date = date("Y-m-d H:i:s", $date);
	$date = vider_date($date); // enlever les valeurs considerees comme nulles (1 1 1970, etc...)
	if (!$date) {
		$date = $defaut;
	}
	return $date;
}
