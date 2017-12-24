<?php
/**
 * Gestion du formulaire de configuration du plugin Rainette.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * On surcharge la fonction de chargement par défaut afin de fournir le service, sa configuration
 * et son état d'exécution au formulaire.
 *
 * @param string $service
 *        Alias du service.
 *
 * @return array
 *        Tableau des données à charger par le formulaire.
 */
function formulaires_configurer_rainette_service_charger($service) {

	// Récupération ou normalisation des données de configuration utilisateur.
	include_spip("services/${service}");
	$configurer = "${service}_service2configuration";
	$configuration = $configurer('service');

	// Normalisation de la configuration utilisateur du service afin d'éviter de gérer
	// au sein du formulaire des valeurs par défaut.
	include_spip('inc/rainette_normaliser');
	$valeurs = parametrage_normaliser($service, $configuration['defauts']);

	// Ajout du service et des éléments utiles de la configuration statique
	$valeurs['service'] = $service;
	$valeurs['nom'] = $configuration['nom'];
	$valeurs['configuration']['termes'] = $configuration['termes'];
	$valeurs['configuration']['enregistrement'] = $configuration['enregistrement'];
	$valeurs['configuration']['offres'] = $configuration['offres'];

	// Ajout des informations d'utilisation du service
	include_spip('inc/config');
	$execution = lire_config("rainette_execution/${service}", array());
	$valeurs['utilisation']['dernier_appel'] = isset($execution['dernier_appel'])
		? $execution['dernier_appel']
		: '';
	$valeurs['utilisation']['compteurs'] = isset($execution['compteurs'])
		? $execution['compteurs']
		: array();

	return $valeurs;
}
