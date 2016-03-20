<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function autoriser_minibando_dist($faire, $quoi, $id, $qui, $opt) {
	include_spip('inc/config');
	$config = lire_config('minibando');
	if (isset($config['limite_webmestre']) and $config['limite_webmestre'] == 'on' and $qui['webmestre'] != 'oui') {
		return false;
	}
	return true;
}
