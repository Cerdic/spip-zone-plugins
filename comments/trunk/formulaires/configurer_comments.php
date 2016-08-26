<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_comments_verifier() {
	$erreurs = array();
	
	if (_request('nom_obli') !== '1') {
		set_request('nom_obli', '0');
	}

	return $erreurs;
}
