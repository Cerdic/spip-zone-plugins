<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Tache periodique de syndication de sites
 *
 * @param array $taches_generales
 * @return array
 */
function speedsyndic_taches_generales_cron($taches_generales){
	spip_log("pipe ?","___speed");
	include_spip("inc/config");
	$config = lire_config("speedsyndic/");
	if (isset($config['frequence'])) {
		$frequence = intval($config['frequence']);
		if ($frequence < 30) {
			$frequence = 30;
		}
		$taches_generales['speedsyndic'] = $frequence;
	}


	return $taches_generales;
}
