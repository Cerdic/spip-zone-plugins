<?php
/**
 * Ce fichier contient les fonctions de service nécessité par le plugin Cache Factory.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoie la configuration spécifique des caches de Rainette.
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return array
 *        Tableau de la configuration brute du plugin Taxonomie.
 */
function rainette_cache_configurer($plugin) {

	// Initialisation du tableau de configuration avec les valeurs par défaut du plugin Cache.
	$configuration = array(
		'racine'          => '_DIR_VAR',
		'sous_dossier'    => true,
		'nom_obligatoire' => array('lieu', 'donnees', 'langage'),
		'nom_facultatif'  => array('unite'),
		'extension'       => '.txt',
		'securisation'    => false,
		'serialisation'   => true,
		'separateur'      => '_' ,
		'conservation'    => 3600 * 24
	);

	return $configuration;
}


/**
 * Effectue le chargement du formulaire de vidage des caches pour le plugin Taxonomie.
 * L'intérêt est de permette le rangement des caches par service.
 *
 * @uses cache_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $configuration
 *        Configuration complète des caches du plugin utilisateur lue à partir de la meta de stockage.
 *
 * @return array
 *         Tableau des valeurs spécifique au plugin taxonomie.
 */
function rainette_cache_vider_charger($plugin, $configuration) {

	$valeurs = array();

	// On constitue la liste des services requis par l'appel
	include_spip('rainette_fonctions');
	$services = rainette_lister_services();

	// On récupère les caches et leur description pour donner un maximum d'explication sur le contenu.
	include_spip('inc/cache');
	foreach ($services as $_service => $_titre) {
		// On récupère les caches du service
		$filtres = array('sous_dossier' => $_service);
		$caches = cache_repertorier('rainette', $filtres);

		// Si il existe des caches pour le service on stocke les informations recueillies
		if ($caches) {
			$valeurs['_caches'][$_service]['titre_service'] = $_titre;
			$valeurs['_caches'][$_service]['caches'] = $caches;
		}
	}

	return $valeurs;
}
