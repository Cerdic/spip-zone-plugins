<?php

/**
 * Filtres pour la gestion de dates
 * Les critères tirées de inc/agenda_filtres.php.
 * Déclares deprecies/obsoletes par le plugin
 *
 * @plugin     Dates outils
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Dates_outils\Filtres
 */

/**
 * Donne les dates d'un intervalle.
 * Par défaut exclus la date fin
 *
 * @param string $date_debut
 *          date de début
 * @param string $date_fin
 *          * date de fin
 * @param number $debut
 *          décalage par rapport à la date de début (offset)
 * @param number $fin
 *          décalage par rapport à la date de fin (offset)
 * @param mixed $horaire
 *          tenir compte d'horraires
 * @param string $format
 *          format de la date
 *
 * @return NULL[]
 */
function dates_intervalle($date_debut, $date_fin, $debut = 0, $fin = 0, $horaire = false, $format = 'Y-m-d H:i:s') {

	if (!is_integer($date_debut)) {
		$date_debut = strtotime($date_debut);
	}
	if (!is_integer($date_fin)) {
		$date_fin = strtotime($date_fin);
	}

	if (empty($format)) {
		$format = 'Y-m-d H:i:s';
	}

	$dates = array();
	if ($date_fin >= $date_debut) {
		$difference = $date_fin - $date_debut;
		$nombre_jours = round($difference / (60 * 60 * 24)) + $fin;
		$i = $debut;
		while ($i <= $nombre_jours) {
			$muliplie = $i * 60 * 60 * 24;
			$date = date('Y-m-d H:i:s', $date_debut + $muliplie);
			if (!$horaire) {
				$date = formater_date($date, 'horaire_zero', $format);
			}
			$dates[] = $date;
			$i ++;
		}
	}

	return $dates;
}

/**
 * Calcule la date para rapport à un décalage donnée
 *
 * @param string $date
 * @param string $decalage
 * @param string $format
 * @return string
 */
function date_relative_brut($date, $decalage, $format = 'Y-m-d H:i:s') {
	return date($format, strtotime($decalage, strtotime($date)));
}

/**
 * formate la date
 *
 * @param string $date
 * @param string $type
 *          pour le moment 'horaire_zero' met l'horaire à 0.
 * @param string $format
 * @return string
 */
function formater_date($date, $type = 'horaire_zero', $format = 'Y-m-d H:i:s') {
	switch ($type) {
		case 'horaire_zero':
			$date = recup_date($date);
			$date = date($format, mktime(0, 0, 0, $date[1], $date[2], $date[0]));
			break;
	}

	return $date;
}

/**
 * Trie les dates
 *
 * @param array $dates
 * @return array
 */
function do_trie_dates (array $dates) {
	usort($dates, "do_compare");
	return $dates;
}

/**
 * cmp()
 * @param int $a
 * @param int $b
 * date comparaison callback
 **/
function do_compare($a, $b) {
	if ($a == $b) return 0;

	return (strtotime($a) < strtotime($b))? -1 : 1;
}

/**
 * Calcule la différence entre deux dates
 *
 * @param string $date_debut
 * @param string $date_fin
 * @param string $type
 *   Type de difference en : annee, mois,jour,heures,minutes ou secondes
 *
 * @return integer
 *   La différence
 */
function dates_difference($date_debut, $date_fin, $type) {
	$debut = new DateTime($date_debut);
	$fin = new DateTime($date_fin);
	$difference_dates = $fin->diff($debut);

	$diviser = 1;
	switch ($type) {
		case 'annee':
			$difference = $difference_dates->y;
			break;
		case 'mois':
			$difference = $difference_dates->m;
			break;
		case 'jour':
			$difference = $difference_dates->d;
			break;
		case 'heures':
			$difference = $difference_dates->h;
			break;
		case 'minutes':
			$difference = $difference_dates->i;
			break;
		case 'secondes':
			$difference = $difference_dates->s;
			break;
	}

	return $difference;
}
