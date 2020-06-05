<?php
/**
 * Plugin Fulltext
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_fulltext_creer_index_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (autoriser('webmestre')) {
		include_spip('inc/fulltext_creer_index');
		list($ok, $erreur) = fulltext_liste_creer_index($arg);
	}

	$GLOBALS['redirect'] = _request('redirect');
	if (!empty($ok)) {
		$GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'], 'ok', $ok);
	}
	if (!empty($erreur)) {
		$GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'], 'erreur', $erreur);
	}
}
