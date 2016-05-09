<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_effacer_configuration_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!autoriser('configurer')) {
		return false;
	}

	if ($arg == 'contact') {
		effacer_meta($arg);
	} else {
		return false;
	}
}
