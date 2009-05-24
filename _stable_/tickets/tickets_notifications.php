<?php

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
	spip_log('notification instituer ticket');
	
	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange");
		return;
	}

	$notifier_publication_ticket = charger_fonction('notifier_publication_ticket','inc');
	$notifier_publication_ticket($id_ticket);
}

function notifications_assignerticket_dist($quoi, $id_ticket, $options) {
	spip_log('notification assigner ticket');
	
	$notifier_assignation_ticket = charger_fonction('notifier_assignation_ticket','inc');
	$notifier_assignation_ticket($id_ticket,$options);
}
?>