<?php
/**
 * Gestion du formulaire générique de vidage des caches d'un plugin donné utilisant Cache Factory.
 *
 * @package    SPIP\CACHE\API
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire affiche la liste des caches issus de l'utilisation du service et propose
 * le vidage de tout ou partie des fichiers.
 *
 * @uses cache_obtenir_configuration()
 * @uses cache_cache_vider_charger()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $options
 *        Tableau d'options qui peut être fourni par un plugin utilisateur uniquement si celui-ci fait appel
 *        au formulaire. La page cache_vider de Cache Factory n'utilise pas ce paramètre.
 *        Le tableau est passé à la fonction de service de chargement du formulaire uniquement.
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 * 		champ de saisie, celle-ci sont systématiquement remises à zéro. Le tableau comprend à minima l'index suivant:
 * 		- `_caches`	: (affichage) liste des descriptions des caches rangés par service
 */
function formulaires_cache_vider_charger($plugin, $options = array()) {

	// Lecture de la configuration des caches du plugin.
	include_spip('inc/cache');
	$configuration = cache_obtenir_configuration($plugin);

	// On appelle le service de chargement des variables qui est soit celui par défaut de Cache Factory
	// soit celui spécifique au plugin si il existe.
	include_spip('cache/cache');
	$valeurs = cache_cache_vider_charger($plugin, $options, $configuration);

	return $valeurs;
}


/**
 * Vérification des saisies : il est indispensable de choisir un cache à supprimer.
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $options
 *        Tableau d'options qui peut être fourni par un plugin utilisateur uniquement si celui-ci fait appel
 *        au formulaire. La page cache_vider de Cache Factory n'utilise pas ce paramètre.
 *        Le tableau est passé à la fonction de service de chargement du formulaire uniquement.
 *
 * @return array
 * 		Tableau des erreurs qui se limite à la non sélection d'au moins un cache.
 */
function formulaires_cache_vider_verifier($plugin, $options = array()) {

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
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $options
 *        Tableau d'options qui peut être fourni par un plugin utilisateur uniquement si celui-ci fait appel
 *        au formulaire. La page cache_vider de Cache Factory n'utilise pas ce paramètre.
 *        Le tableau est passé à la fonction de service de chargement du formulaire uniquement.
 *
 * @return array
 *        Tableau retourné par le formulaire contenant toujours un message de bonne exécution. L'indicateur
 *        editable est toujours à vrai.
 */
function formulaires_cache_vider_traiter($plugin, $options = array()) {

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
