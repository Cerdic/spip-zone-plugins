<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action de connexion
 *
 * @param string $arg Le nom du service
 * @access public
 */
function action_connexion_dist($arg = null) {
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

		// Maintenant qu'on a le token du service, on va demander les
		// informations de connexion
		// Ce tableau doit à minima contenir un champ nom et un champ email
		$info = charger_fonction($type.'_info', 'connecteur');
		$auteur_info = $info($token);

		// Envoyer au pipeline les informations de la personne
		$auteur_info = pipeline('pre_connecteur', $auteur_info);
		// Est-ce que l'email est déjà présent dans la base de donnée ?
		if (empty($auteur_info['email'])) {
			spip_log('aucun email fourni par Facebook', 'facebook'._LOG_ERREUR);
			spip_log($auteur_info, 'facebook'._LOG_ERREUR);
		} else {
			$verifier = charger_fonction('verifier', 'inc');
			if (!$verifier($auteur_info['email'], 'email', array('disponible' => true))) {
				// L'auteur n'est pas encore dans la base de donnée : on le crée
				$auteur = connecteur_creer_auteur($auteur_info);

				// On enregistre le token
				connecteur_save_token($auteur['id_auteur'], $type, $token);

				// On va update la source de l'auteur
				include_spip('action/editer_auteur');
				auteur_modifier($auteur['id_auteur'], array('source' => $type));

				// Et enfin on connecte la personne
				connecteur_connecter($auteur);
			} else {
				// Sinon, on connecte l'auteur
				$auteur = connecteur_connecter($auteur_info);
				connecteur_save_token($auteur['id_auteur'], $type, $token);
			}
		}
		// Envoyer aux pipelines
		$desc = pipeline('post_connecteur', array('auteur' => $auteur, 'info' => $auteur_info));
	}
}
