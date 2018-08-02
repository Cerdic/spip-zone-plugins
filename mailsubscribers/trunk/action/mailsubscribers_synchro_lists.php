<?php
/**
 * Plugin mailsubscribers
 * (c) 2017 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Forcer la synchronisation de toutes les listes
 *
 */
function action_mailsubscribers_synchro_lists_dist() {

	if (!autoriser('creer', 'mailsubscribinglist')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	// lancer le genie de synchro
	$mailsubscribers_synchro_lists = charger_fonction("mailsubscribers_synchro_lists", "genie");
	if ($mailsubscribers_synchro_lists AND function_exists($mailsubscribers_synchro_lists)) {
		$mailsubscribers_synchro_lists(0);
	}
}
