<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Créer un auteur SPIP sur base des donnée de facebook
 *
 * @param mixed $arg
 * @access public
 */
function action_facebook_access_token_auteur_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	include_spip('inc/facebook');
	$token = facebook_access_token();

	facebook_creer_auteur($token);
}
