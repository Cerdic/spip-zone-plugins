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

/**a tâce cron.e crons.
 *
 * @pipeline taches_generales_cron
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function notifications_archive_taches_generales_cron($taches){
	$taches['eliminer_notifications'] = 24*3600; // tous les jours
	return $taches;
}

/**
 * Ajouter les configurations dans celle de réservation événements.
 *
 * @pipeline reservation_evenement_objets_configuration
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function notifications_archive_reservation_evenement_objets_configuration($flux) {

	$objets = array(
		'notifications_archive' => array(
			'label' => _T('notifications_archive:notifications_archive_titre'),
		),
	);

	$flux['data'] = array_merge($flux['data'], $objets);

	return $flux;
}


?>