<?php
/**
 * Ce fichier contient l'action `client_retirer_serveur` utilisée par un site client pour
 * retirer un serveur donné de la liste des serveurs consultables.
 *
 * @package SPIP\BOUSSOLE\Serveur\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet au site client de retirer un serveur donné de sa liste des serveurs
 * qu'il est autorisé à interroger (variable de configuration).
 *
 * Cette action est réservée aux webmestres. Elle nécessite un seul argument, le nom du serveur
 * à retirer.
 *
 * @uses boussole_actualiser_boussoles()
 *
 * @return void
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
			// Retrait du serveur de la configuration client
			unset($serveurs[$alias_serveur]);
			ecrire_config('boussole/client/serveurs_disponibles', $serveurs);

			spip_log("ACTION RETRAIT SERVEUR : alias = ". $alias_serveur, 'boussole' . _LOG_INFO);
		}
	}
}

?>