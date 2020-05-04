<?php
/**
 * Utilisations de pipelines par Kit de maintenance
 *
 * @plugin     Kit de maintenance
 * @copyright  2020
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Maintenancekit\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



// Diverses taches de maintenance
function maintenancekit_taches_generales_cron($taches){
	$taches['maintenancekit_recalculer_status_rubriques'] = 24*3600*365*10;  // tous les 10 ans
	return $taches;
}