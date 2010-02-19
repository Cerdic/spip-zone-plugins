<?php
// pour le pipeline
function tickets_notifications() {}

/**
 * 
 * Fonction appelée lors du changement de statut d'un ticket
 * 
 * @return 
 * @param object $quoi
 * @param object $id_article
 * @param object $options
 */
 


function notifications_instituerticket_dist($quoi, $id_ticket, $options) {
	
	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		return;
	}

	$notifier_publication_ticket = charger_fonction('notifier_publication_ticket','inc');
	$notifier_publication_ticket($id_ticket,$options['statut'],$options['statut_ancien']);
}

function notifications_assignerticket_dist($quoi, $id_ticket, $options) {
	
	$notifier_assignation_ticket = charger_fonction('notifier_assignation_ticket','inc');
	$notifier_assignation_ticket($id_ticket,$options);
}
?>
