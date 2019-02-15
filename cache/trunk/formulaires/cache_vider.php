<?php
/**
 * Gestion du formulaire de vidage des caches d'un plugin donné utilisant Cache Factory.
 *
 * @package    SPIP\CACHE\API
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire affiche la liste des caches issus de l'utilisation du service et propose
 * le vidage de tout ou partie des fichiers.
 *
 * @uses cache_obtenir_configuration()
 * @uses cache_cache_configurer()
 * @uses cache_cache_vider_charger()
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 * 		champ de saisie, celle-ci sont systématiquement remises à zéro.
 * 		- `_caches`	: (affichage) liste des descriptions des caches rangés par service
 */
function formulaires_cache_vider_charger($plugin) {

	// Lecture de la configuration des caches du plugin.
	include_spip('inc/cache');
	$configuration = cache_obtenir_configuration($plugin);

	// On appelle le service de chargement des variables qui est soit celui par défaut de Cache Factory
	// soit celui spécifique au plugin si il existe.
	include_spip('cache/cache');
	$valeurs = cache_cache_vider_charger($plugin, $configuration);

	return $valeurs;
}


/**
 * Vérification des saisies : il est indispensable de choisir un cache à supprimer.
 *
 * @return array
 * 		Tableau des erreurs qui se limite à la non sélection d'au moins un cache.
 */
function formulaires_cache_vider_verifier($plugin) {

	$erreurs = array();

	$obligatoires = array('caches');
	foreach ($obligatoires as $_obligatoire) {
		if (!_request($_obligatoire))
			$erreurs[$_obligatoire] = _T('info_obligatoire');
	}

	return $erreurs;
}

/**
 * Exécution du formulaire : la liste des caches sélectionnés est récupérée et fournie à l'API cache pour suppression.
 *
 * @uses cache_vider()
 *
 * @return array
 *        Tableau retourné par le formulaire contenant toujours un message de bonne exécution. L'indicateur
 *        editable est toujours à vrai.
 */
function formulaires_cache_vider_traiter($plugin) {

	$retour = array();

	// On récupère les caches à supprimer
	$caches = _request('caches');

	// On appelle l'API des caches
	include_spip('inc/cache');
	cache_vider($plugin, $caches);

	$retour['message_ok'] = _T('cache:cache_vider_succes');
	$retour['editable'] = true;

	return $retour;
}
