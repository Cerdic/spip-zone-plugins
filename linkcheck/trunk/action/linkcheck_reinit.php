<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_linkcheck_reinit_dist() {
	include_spip('inc/autoriser');
	include_spip('inc/config');

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (autoriser('reinitialiser', 'linkcheck')) {
		sql_delete('spip_linkchecks');
		sql_delete('spip_linkchecks_liens');
		ecrire_config('linkcheck_dernier_id_objet', 0);
		ecrire_config('linkcheck_dernier_objet', 0);
	}

	if ($redirect = _request('redirect')) {
		include_spip('inc/headers');
		redirige_par_entete($redirect.'&message=delete_ok');
	}
}
