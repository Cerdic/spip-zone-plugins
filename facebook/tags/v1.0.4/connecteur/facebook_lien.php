<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function connecteur_facebook_lien_dist($action) {
	include_spip('inc/facebook');
	return facebook_lien_connection($action);
}
