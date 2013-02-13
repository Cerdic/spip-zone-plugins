<?php
/**
 * Plugin Spip2spip
 * (c) 2013 erational
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


// les taches crons
function spip2spip_taches_generales_cron($taches_generales){   
  $taches_generales['spip2spip_syndic'] = 60*5;  // tous les 5 min		
	return $taches_generales;
}

?>