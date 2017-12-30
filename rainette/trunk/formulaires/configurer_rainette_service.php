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

	// Récupération et normalisation des données de configuration utilisateur.
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
	$valeurs['_configuration']['termes'] = $configuration['termes'];
	$valeurs['_configuration']['enregistrement'] = $configuration['enregistrement'];
	$valeurs['_configuration']['offres'] = $configuration['offres'];

	// Ajout des informations d'utilisation du service
	include_spip('inc/config');
	$execution = lire_config("rainette_execution/${service}", array());
	$valeurs['_utilisation']['dernier_appel'] = isset($execution['dernier_appel'])
		? $execution['dernier_appel']
		: '';
	$valeurs['_utilisation']['compteurs'] = isset($execution['compteurs'])
		? $execution['compteurs']
		: array();

	// Gestion des thèmes locaux et distants.
	include_spip('rainette_fonctions');
	$valeurs['_themes']['distants'] = rainette_lister_themes($service, 'api');
	$valeurs['_themes']['locaux'] = rainette_lister_themes($service, 'local');
	$valeurs['_themes']['weather'] = !in_array($service, array('weather', 'owm', 'weatherbit'))
		? rainette_lister_themes('weather', 'local')
		: array();

	// On positionne le meta casier car la fonction de recensement automatique n'est plus appelée ni ne pourrait
	// fonctionner avec un hidden dont la valeur est dynamique (utilisation d'une variable d'environnement #ENV).
	$valeurs['_meta_casier'] = "rainette/${service}";

	return $valeurs;
}
