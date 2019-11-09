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
	// -- Pas de cache pour l'index des collections
	$configuration = array(
		'racine'          => '_DIR_VAR',
		'sous_dossier'    => true,
		'nom_obligatoire' => array('type_requete', 'collection'),
		'nom_facultatif'  => array('complement'),
		'extension'       => '.json',
		'securisation'    => false,
		'serialisation'   => false,
		'decodage'        => true,
		'separateur'      => '-' ,
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
function ezrest_formulaire_charger($plugin, $configuration) {

	$valeurs = array();

	// On constitue la liste des types de requêtes admises pour regrouper les caches selon ce premier critère.
	$types_requete = array('index', 'collection', 'ressource');

	// On récupère les caches et leur description pour donner un maximum d'explication sur le contenu.
	include_spip('inc/cache');
	foreach ($types_requete as $_type) {
		// On récupère les caches du service
		$filtres = array('type_requete' => $_type);
		$caches = cache_repertorier('ezrest', $filtres);

		// Présentation des filtres pour les collections
		if ($_type != 'index') {
			foreach ($caches as $_cle => $_cache) {
				if ($_type == 'ressource') {
					$caches[$_cle]['ressource'] = $_cache['complement'];
				} else {
					// On traite le complément des filtres pour un affichage plus clair
					$caches[$_cle]['filtre'] = !empty($_cache['complement'])
						? str_replace(array('_p_', '_e_', '_s_'), array(' | ', '=', '/'), $_cache['complement'])
						: '';
				}
			}
		}

		// Si il existe des caches pour le service on stocke les informations recueillies
		if ($caches) {
			$valeurs['_caches'][$_type]['titre_type'] = _T('ezrest:type_requete_' . $_type . '_titre');
			$valeurs['_caches'][$_type]['caches'] = $caches;
		}
	}

	return $valeurs;
}
