<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function connecteur_facebook_lien_dist() {
	include_spip('inc/facebook');
	return facebook_lien_connection('connection');
}
