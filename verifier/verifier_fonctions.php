<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function filtre_verifier_lister_disponibles_dist($repertoire = 'verifier') {
	include_spip('inc/verifier');

	return verifier_lister_disponibles($repertoire);
}