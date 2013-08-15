<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action de suppression en base de donnees de la boussole
 *
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
		include_spip('inc/deboussoler');
		boussole_supprimer($alias);
		spip_log("ACTION SUPPRIMER BOUSSOLE : alias = ". $alias, 'boussole' . _LOG_INFO);
	}
}

?>