<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de génération des caches des boussoles hébergées et de la liste de ces boussoles
 *
 */
function action_serveur_actualiser_caches_dist(){

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

	// Actualisation de tous les caches du serveur
	if ($mode === 'tout') {
		include_spip('inc/cacher');
		boussole_actualiser_caches();
	}
}

?>