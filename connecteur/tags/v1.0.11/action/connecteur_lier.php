<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_connecteur_lier_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	include_spip('inc/session');
	include_spip('connecteur_fonctions');
	include_spip('inc/token');

	// Ejecter les personnes non connectée
	if (empty(session_get('id_auteur'))) {
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_interdit'));
		die();
	}

	// Type de connection à effectuer
	$type = $arg;

	// Charger la configuration de la connection
	$connecteur_config = charger_fonction($type.'_config', 'connecteur');
	$config = $connecteur_config();

	// Type de connecteur "token"
	if ($config['connecteur'] == 'token') {
		// Retrouver le token en executant la fonction renseignée dans la config
		if (isset($config['charger_fichier'])) {
			include_spip($config['charger_fichier']);
		}
		$trouver_token = $config['trouver_token'];
		$token = $trouver_token();

		// On enregistre le token
		connecteur_save_token(session_get('id_auteur'), $type, $token);
	}
}
