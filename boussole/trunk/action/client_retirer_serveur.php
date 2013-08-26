<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action de retrait d'un serveur de la liste des serveurs configurés
 * comme disponibles pour le site client
 *
 */
function action_client_retirer_serveur_dist(){

	// Securisation et autorisation car c'est une action auteur:
	// -> argument attendu est l'alias du serveur
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$alias_serveur = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Suppression du serveur connu par son alias. On ne supprime jamais le serveur "spip"
	if ($alias_serveur AND ($alias_serveur != 'spip')) {
		include_spip('inc/config');
		$serveurs = lire_config('boussole/client/serveurs_disponibles');
		if (isset($serveurs[$alias_serveur])) {
			unset($serveurs[$alias_serveur]);
			ecrire_config('boussole/client/serveurs_disponibles', $serveurs);
			spip_log("ACTION RETRAIT SERVEUR : alias = ". $alias_serveur, 'boussole' . _LOG_INFO);
		}
	}
}

?>