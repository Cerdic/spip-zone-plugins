<?php
/**
 * Ce fichier contient les fonctions de service nécessitées par le plugin Cache Factory.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoie la configuration spécifique des caches gérés par REST Factory si les fonctions de collection des données
 * sont directement codés en PHP.
 *
 * Dans le cas où les données JSON sont créées via des squelettes SPIP, le cache est déjà géré par SPIP.
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return array
 *        Tableau de la configuration brute du plugin Taxonomie.
 */
function ezrest_cache_configurer($plugin) {

	// Initialisation du tableau de configuration avec les valeurs par défaut du plugin Cache.
	$configuration = array(
		'racine'          => '_DIR_VAR',
		'sous_dossier'    => true,
		'nom_obligatoire' => array('requete'),
		'nom_facultatif'  => array('hash'),
		'extension'       => '.json',
		'securisation'    => false,
		'serialisation'   => false,
		'decodage'        => true,
		'separateur'      => '_' ,
		'conservation'    => 3600 * 24
	);

	return $configuration;
}
