<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Supprimer un token chez l'utilisateur.
 *
 * ```
 * [(#BOUTON_ACTION{<:deconnecter_facebook:>, #URL_ACTION_AUTEUR{connecteur_delier, facebook, #SELF}, '', confirmer})]
 * ```
 *
 * @param mixed $arg
 * @access public
 */
function action_connecteur_delier_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$type = $arg;

	include_spip('inc/token');
	include_spip('inc/session');
	connecteur_delete_token(session_get('id_auteur'), $type);
}
