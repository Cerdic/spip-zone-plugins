<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_reinitialiser_seances_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if ($arg == 'ok' ) {
		sql_delete('spip_seances');
	}
}
?>