<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// boucles
include_spip('public/simplecal_boucles');

// filtres
include_spip('inc/simplecal_filtres');

// balises 
include_spip('balise/simplecal_dates');

// critères
include_spip('public/simplecal_criteres');

?>