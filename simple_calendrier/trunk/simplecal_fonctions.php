<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3
 * Licence GNU/GPL
 * 2010-2016
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// boucles
include_spip('public/simplecal_boucles');

// filtres
include_spip('inc/simplecal_filtres');

// balises 
include_spip('balise/simplecal_dates');

// criteres
include_spip('public/simplecal_criteres');

?>