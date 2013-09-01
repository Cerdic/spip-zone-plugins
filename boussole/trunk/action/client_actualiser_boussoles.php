<?php
/**
 * Ce fichier contient l'action `client_actualiser_boussoles` utilisée par un site client pour
 * actualiser l'ensemble des boussoles installées.
 *
 * @package SPIP\BOUSSOLE\Serveur\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet au site client d'actualiser l'ensemble des boussoles insérées dans
 * sa base de données.
 *
 * Cette action est réservée aux webmestres. Elle nécessite un seul argument dont la valeur
 * est toujours égale à "tout".
 * L'action est utilisée par le CRON journalier du client ou sur demande depuis la page
 * d'administration du client de l'espace privé.
 *
 * @note
 * 		Aucun argument n'est en fait nécessaire. Néanmoins, étant donné le bug de la balise
 * 		`#URL_ACTION_AUTEUR` uniquement corrigé dans la version 3.0.12, il est nécessaire
 * 		d'en passer un bidon. On s'en sert donc proprement en passant l'argument "tout"
 * 		et en le testant.
 *
 * @uses boussole_actualiser_boussoles()
 *
 * @return void
 */
function action_client_actualiser_boussoles_dist(){

	// Securisation: aucun argument attendu, néanmoins étant donné le bug de la balise
	// #URL_ACTION_AUTEUR, il est nécessaire d'en passer un bidon.
	// On s'en sert donc proprement en passant l'argument "tout" et en le testant
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$mode = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Actualisation de toutes les boussoles installées sur le site client
	if ($mode === 'tout') {
		include_spip('inc/client');
		boussole_actualiser_boussoles();
	}
}

?>