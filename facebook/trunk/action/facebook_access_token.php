<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_facebook_access_token_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	include_spip('inc/facebook');
	facebook_access_token();
}
