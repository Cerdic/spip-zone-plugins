<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2013 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_zengarden_activer_switcher_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	include_spip('inc/meta');
	if ($arg=='on'){
		ecrire_meta("zengarden_switcher", 'on');
	}
	else {
		effacer_meta("zengarden_switcher");
	}
}

?>
