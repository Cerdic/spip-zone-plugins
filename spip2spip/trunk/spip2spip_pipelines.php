<?php
/**
 * Plugin Spip2spip
 * (c) 2013 erational
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


// les taches crons
function spip2spip_taches_generales_cron($taches_generales){   
	//Recuperation de la configuration
	$conf = @unserialize($GLOBALS['meta']['spip2spip']);
	if(is_array($conf) AND intval($conf['intervalle_cron']) > 1){
		$taches_generales['spip2spip_syndic'] = 60 * intval($conf['intervalle_cron']);
	}else{
		$taches_generales['spip2spip_syndic'] = 60*5;  // tous les 5 min par defaut
	}	
	return $taches_generales;
}

?>