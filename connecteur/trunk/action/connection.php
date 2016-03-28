<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_connection_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	include_spip('connecteur_fonctions');

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

		// Maintenant qu'on a le token du service, on va demander les informations de connection
		// Ce tableau doit à minima contenir un champ nom et un champ email
		$info = charger_fonction($type.'_info', 'connecteur');
		$auteur_info = $info($token);

		// Est-ce que l'email est déjà présent dans la base de donnée ?
		$verifier = charger_fonction('verifier', 'inc');
		if (!$verifier($auteur_info['email'], 'email', array('disponible' => true))) {

			// L'auteur n'est pas encore dans la base de donnée : on le crée
			$auteur = connecteur_creer_auteur($auteur_info);
			// On va update la source de l'auteur
			include_spip('action/editer_auteur');
			auteur_modifier($auteur['id_auteur'], array('source' => $type));
			connecteur_connecter($auteur);

		} else {
			// Sinon, on connecte l'auteur
			connecteur_connecter($auteur_info);
		}
	}
}
