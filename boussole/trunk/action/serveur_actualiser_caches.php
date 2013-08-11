<?php
/**
 * Action de mises a jour en base de donnees des plugins du depot
 *
 */
function action_serveur_actualiser_caches_dist(){

	// Securisation: aucun argument attendu
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Actualisation de tous les caches du serveur
	include_spip('inc/cacher');
	boussole_actualiser_caches();

}

?>