<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Supprimer un hash de traduction
 *
 * @return void
 */
function action_traduiretexte_supprimer_trad_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	sql_delete('spip_traductions', array('hash=' . sql_quote($arg)));
}
