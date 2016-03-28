<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action de connexion d'un compte spécifique au site.
 * Les tokens spécifiques au site sont en id_auteur = 0
 *
 * @param string $arg Le nom du service
 * @access public
 */
function action_connexion_site_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	include_spip('connecteur_fonctions');
	include_spip('inc/token');

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

		// On sauvegarde un token en id_auteur = 0
		// Cela symbolise le token du site
		connecteur_save_token(0, $type, $token);
	}
}
