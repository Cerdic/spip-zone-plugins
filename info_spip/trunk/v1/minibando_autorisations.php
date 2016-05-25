<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function minibando_autoriser() {
}

function autoriser_minibando_dist($faire, $quoi, $id, $qui, $opt) {
	include_spip('inc/config');
	$config = lire_config('minibando');
	if (isset($config['limite_webmestre']) and $config['limite_webmestre'] == 'on' and $qui['webmestre'] != 'oui') {
		return false;
	}

	return true;
}
