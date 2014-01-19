<?php
/**
 * Utilisations de pipelines par Archive notifications
 *
 * @plugin     Archive notifications
 * @copyright  2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Notifications_archive\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

function notifications_archive_taches_generales_cron($taches){
    $taches['eliminer_notifications'] = 24*3600; // tous les jours
    return $taches;
}



?>