<?php
/**
 * Définit les autorisations du plugin DayFill
 *
 * @plugin     DayFill
 * @copyright  2014
 * @author     Cyril Marion
 * @licence    GNU/GPL
 * @package    SPIP\Dayfill\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Calculer la durée entre 2 dates en H:i:s
 *
 * @param string $date_fin
 *        Le format attendu est 0000-00-00 00:00:00
 * @param string $date_debut
 *        Le format attendu est 0000-00-00 00:00:00
 * @param bool $decimal
 *        Par défaut `false`, retournera la durée sous la forme H:i:s
 *        Si `true`, retournera la durée sous une forme décimale à 2 chiffres après la virgule
 * @return string|float
 */
function calcul_duree ($date_fin, $date_debut, $decimal = false)
{
	$time1 = strtotime($date_fin);
	$time2 = strtotime($date_debut);

	$time = $time1 - $time2;
	$heures = floor($time / 3600) ; // on arrondit à l'entier inférieur
	$minutes = floor(($time - $heures * 3600) / 60); // on arrondit à l'entier inférieur
	$secondes = ($time - (($heures * 3600) + ($minutes * 60)));

	$result = $heures . ':' . $minutes .':' . $secondes;
	if ($decimal) {
		$result = $heures + round(($minutes / 60), 2);
	}

	return $result;
}

/**
 * Calculer la durée en jour.homme d'une activité
 *
 * @param string $date_fin
 *        Le format attendu est 0000-00-00 00:00:00
 * @param string $date_debut
 *        Le format attendu est 0000-00-00 00:00:00
 * @return float
 *        La durée sous la forme d'un nombre à virgule
 */
function calcul_jour_homme ($date_fin, $date_debut)
{
	$time1 = strtotime($date_fin);
	$time2 = strtotime($date_debut);
	$jour_homme = (8*3600); // 8 heures par jour

	$time = $time1 - $time2;
	$heures = floor($time / 3600) ; // on arrondit à l'entier inférieur
	$minutes = floor(($time - $heures * 3600) / 60); // on arrondit à l'entier inférieur
	$secondes = ($time - (($heures * 3600) + ($minutes * 60)));

	$result = $heures + ($minutes / 60);

	return round($time/$jour_homme, 2);
}

?>