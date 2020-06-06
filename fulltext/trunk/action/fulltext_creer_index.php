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

	$oks = $erreurs = array();
	if (autoriser('webmestre')) {
		include_spip('inc/fulltext_creer_index');
		list($oks, $erreurs) = fulltext_liste_creer_index($arg);
	}

	$GLOBALS['redirect'] = _request('redirect');
	if ($oks) {
		$GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'], 'ok', $oks);
	}
	if ($erreurs) {
		$GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'], 'erreur', $erreurs);
	}
}
