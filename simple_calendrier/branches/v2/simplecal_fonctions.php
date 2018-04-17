<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3
 * Licence GNU/GPL
 * 2010-2016
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

// boucles
include_spip('public/simplecal_boucles');

// filtres
include_spip('inc/simplecal_filtres');

// balises 
include_spip('balise/simplecal_dates');

// criteres
include_spip('public/simplecal_criteres');

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
function time_dateplus($date, $secondes, $format = 'Y-m-d H:i:s') {
	$date = strtotime($date)+eval("return $secondes;"); // permet de passer une expression
	return date($format, $date);
}
