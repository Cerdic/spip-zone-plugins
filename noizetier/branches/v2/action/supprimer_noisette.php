<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_supprimer_noisette_dist($id_noisette = null) {
	if (is_null($id_noisette)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_noisette = $securiser_action();
	}

	if (intval($id_noisette)) {
		include_spip('noizetier_fonctions');
		noizetier_supprimer_noisette($id_noisette);
	}
}
