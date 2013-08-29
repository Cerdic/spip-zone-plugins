<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action de retrait d'une boussole manuelle de la configuration du serveur.
 *
 * La configuration du plugin contient la liste des boussoles manuelles hébergées par le serveur.
 * Cette action retire la boussole désignée de cette configuration et lance l'actualisation
 * des caches pour assurer la cohérence avec la nouvelle configuration.
 *
 * @package BOUSSOLE\Serveur\Action
 *
 * @return void
 */
function action_serveur_retirer_boussole_dist(){

	// Securisation et autorisation car c'est une action auteur:
	// -> argument attendu est l'alias du serveur
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$alias_boussole = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Suppression du serveur connu par son alias. On ne supprime jamais le serveur "spip"
	if ($alias_boussole) {
		include_spip('inc/config');
		$boussoles_manuelles = lire_config('boussole/serveur/boussoles_disponibles');
		if (isset($boussoles_manuelles[$alias_boussole])) {
			// Retrait de la boussole manuelle de la configuration du serveur
			unset($boussoles_manuelles[$alias_boussole]);
			ecrire_config('boussole/serveur/boussoles_disponibles', $boussoles_manuelles);

			// Mise à jour des caches en conséquence
			include_spip('inc/serveur');
			boussole_actualiser_caches();

			spip_log("ACTION RETRAIT BOUSSOLE MANUELLE : alias = ". $alias_boussole, 'boussole' . _LOG_INFO);
		}
	}
}

?>