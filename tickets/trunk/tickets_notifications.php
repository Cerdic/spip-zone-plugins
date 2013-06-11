<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 * @package SPIP\Tickets\Notifications
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// pour le pipeline
function tickets_notifications(){}

/**
 * 
 * Fonction de notification appelée lors du changement de statut d'un ticket
 * 
 * @return 
 * @param object $quoi
 * @param object $id_article
 * @param object $options
 */
function notifications_instituerticket_dist($quoi, $id_ticket, $options) {
	
	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log('on ne notifie pas car même statut','test.'._LOG_ERREUR);
		return;
	}

	$notifier_publication_ticket = charger_fonction('notifier_publication_ticket','inc');
	$notifier_publication_ticket($id_ticket,$options['statut'],$options['statut_ancien']);
}

/**
 * 
 * Fonction de notification appelée lors du changement d'assignation d'un ticket
 * 
 * @return 
 * @param object $quoi
 * @param object $id_article
 * @param object $options
 */
function notifications_assignerticket_dist($quoi, $id_ticket, $options) {
	
	$notifier_assignation_ticket = charger_fonction('notifier_assignation_ticket','inc');
	$notifier_assignation_ticket($id_ticket,$options);
}

?>
