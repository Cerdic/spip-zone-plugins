<?php
/**
 * Plugin Mots de passe expirables
 * (c) 2013 erational
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


// les taches crons
function motpasseexpirable_taches_generales_cron($taches_generales){   
  $taches_generales['motpasseexpirable'] = 60*60*24;  // tous les jours
	return $taches_generales;
}

?>