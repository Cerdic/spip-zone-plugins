<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function abonnement_I2_cfg_form($flux) {
    $flux .= recuperer_fond('fonds/inscription2_abonnement');
	return ($flux);
}

?>
