<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

include_spip('public/agenda');

/**
 * Ajout d'un offset a une date
 *
 * @param string $date
 * @param int $secondes
 *   peut etre une expression math : 24*60*60
 * @param string $format
 *   format de sortie de la date
 * @return string
 */
function agenda_dateplus($date,$secondes,$format="Y-m-d H:i:s"){
	$date = strtotime($date)+eval("return $secondes;"); // permet de passer une expression
	return date($format,$date);
}

/**
 * decale les mois de la date.
 * cette fonction peut raboter le jour si le nouveau mois ne les contient pas
 * exemple 31/01/2007 + 1 mois => 28/02/2007
 *
 * @param string $date
 * @param int $decalage
 * @param string $format
 * @return string
 */
function agenda_moisdecal($date,$decalage,$format="Y-m-d H:i:s"){
	include_spip('inc/filtres');
	$date_array = recup_date($date);
	if ($date_array) list($annee, $mois, $jour) = $date_array;
	if (!$jour) $jour=1;
	if (!$mois) $mois=1;
	$mois2 = $mois + $decalage;
	$date2 = mktime(1, 1, 1, $mois2, $jour, $annee);
	// mois normalement attendu
	$mois3 = date('m', mktime(1, 1, 1, $mois2, 1, $annee));
	// et si le mois de la nouvelle date a moins de jours...
	$mois2 = date('m', $date2);
	if ($mois2 - $mois3) $date2 = mktime(1, 1, 1, $mois2, 0, $annee);
	return date($format, $date2);
}


/**
 * decale les jours de la date.
 *
 * @param string $date
 * @param int $decalage
 * @param string $format
 * @return string
 */
function agenda_jourdecal($date,$decalage,$format="Y-m-d H:i:s"){
	include_spip('inc/filtres');
	$date_array = recup_date($date);
	if ($date_array) list($annee, $mois, $jour) = $date_array;
	if (!$jour) $jour=1;
	if (!$mois) $mois=1;
	$jour2 = $jour + $decalage;
	$date2 = mktime(1, 1, 1, $mois, $jour2, $annee);
	return date($format, $date2);
}

/**
 * Filtre pour tester si une date est dans le futur
 * [(#DATE|agenda_date_a_venir) Dans le futur...]
 *
 * @param string $date_test
 * @param string $date_ref
 *   date de reference, par defaut celle du serveur (argument utile pour les tests unitaires)
 * @return string
 */
function agenda_date_a_venir($date_test,$date_ref=null){
	if (is_null($date_ref))
		$date_ref = $_SERVER['REQUEST_TIME'];
	else
		$date_ref = strtotime($date_ref);

	return (strtotime($date_test)>$date_ref)?' ':'';
}


/**
 * Filtre pour tester si une date est dans le passe
 * [(#DATE|agenda_date_passee) Dans le passe...]
 *
 * @param string $date_test
 * @param string $date_ref
 *   date de reference, par defaut celle du serveur (argument utile pour les tests unitaires)
 * @return string
 */
function agenda_date_passee($date_test,$date_ref=null){
	if (is_null($date_ref))
		$date_ref = $_SERVER['REQUEST_TIME'];
	else
		$date_ref = strtotime($date_ref);

	return (strtotime($date_test)<$date_ref)?' ':'';
}


/**
 * Raccourcis [->evenement12] et [->evt12]
 */
/*
function generer_url_evenement($id, $args='', $ancre='') {
	return array('evenement', $id);
}*/

?>