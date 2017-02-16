<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_deplacer_noisette_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if ($arg) {
		include_spip('noizetier_fonctions');
		preg_match('/^([\d]+)-(bas|haut)$/', $arg, $arg);
		array_shift($arg);
		list($id_noisette, $sens) = $arg;
		noizetier_deplacer_noisette($id_noisette, $sens);
	}
}
