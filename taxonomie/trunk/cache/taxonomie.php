<?php
/**
 * Ce fichier contient les fonctions de service nécessité par le plugin Cache Factory.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoie la configuration spécifique des caches de Taxonomie.
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return array
 *        Tableau de la configuration brute du plugin Taxonomie.
 */
function taxonomie_cache_configurer($plugin) {

	// Initialisation du tableau de configuration avec les valeurs par défaut du plugin Cache.
	$configuration = array(
		'racine'          => _DIR_VAR,
		'sous_dossier'    => false,
		'nom_obligatoire' => array('tsn', 'service', 'action', 'language'),
		'nom_facultatif'  => array('section'),
		'extension'       => '.txt',
		'securisation'    => false,
		'serialisation'   => true,
		'separateur'      => '_'
	);

	return $configuration;
}


/**
 * Complète la description canonique d'un cache.
 * Le plugin Taxonomie rajoute le nom scientifique du taxon.
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $cache
 *       Tableau identifiant le cache pour lequel on veut construire le nom.
 * @param string $fichier_cache
 *        Fichier cache désigné par son chemin complet.
 * @param array  $configuration
 *        Configuration complète des caches du plugin utilisateur lue à partir de la meta de stockage.
 *
 * @return array
 *         Description du cache complétée par un ensemble de données propres au plugin.
 */
function taxonomie_cache_completer($plugin, $cache, $fichier_cache, $configuration) {

	// Tableau des taxons pour éviter de faire des appels SQL à chaque cache.
	static $taxons = array();

	// On rajoute le nom scientifique du taxon pour un éventuel affichage.
	// Si le taxon a été supprimé de la base le nom ne sera pas trouvé.
	if (isset($cache['tsn'])) {
		if (!isset($taxons[$cache['tsn']])) {
			// Si pas encore stocké, on cherche le nom scientifique du taxon et on le sauvegarde.
			$where = array('tsn=' . intval($cache['tsn']));
			$taxons[$cache['tsn']] = '';
			if ($nom = sql_getfetsel('nom_scientifique', 'spip_taxons', $where)) {
				$taxons[$cache['tsn']] = $nom;
			}
		}
		$cache['nom_scientifique'] = $taxons[$cache['tsn']];
	}

	return $cache;
}
