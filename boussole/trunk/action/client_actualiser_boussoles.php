<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de génération des caches des boussoles hébergées et de la liste de ces boussoles
 *
 */
function action_client_actualiser_boussoles_dist(){

	// Securisation: aucun argument attendu, néanmoins étant donné le bug la balise
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