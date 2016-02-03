<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function minibando_styliser($flux) {

	if ($flux['args']['fond'] == 'formulaires/administration') {
		include_spip('inc/autoriser');
		if (!autoriser('minibando')) {
			$flux['data'] = 'squelettes-dist/formulaires/administration';
		}
	}

	return $flux;
}
