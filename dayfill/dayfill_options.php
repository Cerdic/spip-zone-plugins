<?php
/**
 * Plugin DayFill - Gestionnaire de temps pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 *
 */

spip_log('options bien chargées', 'dayfile');

//action_purger_dist();

// invalider le cache pour la meta dayfill (?)
$GLOBALS['marqueur'] .= ":".md5($GLOBALS['meta']['dayfill']);


?>