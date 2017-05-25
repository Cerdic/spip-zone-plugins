<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_supprimer_composition_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$page = $securiser_action();
	if ($page) {
		include_spip('inc/config');
		effacer_config("noizetier_compositions/${page}");
	}
}
