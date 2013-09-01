<?php
/**
 * Ce fichier contient l'action `client_supprimer_boussole` utilisée par un site client pour
 * supprimer de façon sécurisée une boussole donnée.
 *
 * @package SPIP\BOUSSOLE\Serveur\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet au site client de supprimer de sa base de données, de façon sécurisée,
 * une boussole donnée.
 *
 * Cette action est réservée aux webmestres. Elle nécessite un seul argument,
 * l'alias de la boussole.
 *
 * @uses boussole_actualiser_boussoles()
 *
 * @return void
 */
function action_client_supprimer_boussole_dist(){

	// Securisation et autorisation car c'est une action auteur:
	// -> argument attendu est l'alias de la boussole
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$alias = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Suppression de la boussole connue par son alias
	if ($alias) {
		include_spip('inc/client');
		boussole_supprimer($alias);
		spip_log("ACTION SUPPRIMER BOUSSOLE : alias = ". $alias, 'boussole' . _LOG_INFO);
	}
}

?>