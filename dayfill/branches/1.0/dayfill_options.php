<?php
/**
 * Plugin DayFill - Gestionnaire d'activités pour Spip
 * Licence GPL (c) 2010 - Ateliers CYM
 *
 */

spip_log('options bien charg�es', 'dayfile');

//action_purger_dist();

// invalider le cache pour la meta dayfill (?)
$GLOBALS['marqueur'] .= ":".md5($GLOBALS['meta']['dayfill']);


?>
