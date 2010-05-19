<?php
/*
 * Plugin C'est chaud
 * (c) 2010 Fil
 * Distribue sous licence GPL
 *
 */

//
// Alerte sur les articles publies post-dates
//
function genie_chaud_dist($last) {
	$cfg = @unserialize($GLOBALS['meta']['chaud']);

	include_spip('inc/chaud');
	chaud_notifier();

	return 1;
}

function chaud_taches_generales_cron($taches_generales){
#	if ($cfg = @unserialize($GLOBALS['meta']['chaud'])
#	AND $cfg['notifier']) {
		// surveiller toutes les heures les publications chaudes
		$taches_generales['chaud'] = 3600;
#	}
	return $taches_generales;
}

?>