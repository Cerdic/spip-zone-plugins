<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Enregistrer le compte Facebook du site.
 *
 * @access public
 */
function action_facebook_access_token_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	include_spip('inc/facebook');
	$token = facebook_access_token();

	// Ecrire le token dans les meta du site
	ecrire_config('facebook/accessToken', $token);
}
