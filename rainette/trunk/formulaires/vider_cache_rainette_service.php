<?php
/**
 * Gestion du formulaire de vidage des caches d'un service donné.
 *
 * @package    SPIP\RAINETTE\CACHE
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire affiche la liste des caches issus de l'utilisation du service et propose
 * le vidage de tout ou partie des fichiers.
 *
 * @uses cache_repertorier()
 *
 * @param string $service
 *        Alias du service.
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 * 		champ de saisie, celle-ci sont systématiquement remises à zéro.
 * 		- `_caches`	: (affichage) liste des descriptions des caches rangés par service
 */
function formulaires_vider_cache_rainette_service_charger($service) {

	$valeurs = array();

	// On récupère les caches et leur description pour donner un maximum d'explication sur le contenu.
	include_spip('inc/rainette_normaliser');
	$valeurs['_caches'] = cache_repertorier($service);

	return $valeurs;
}

/**
 * Vérification des saisies : il est indispensable de choisir un cache à supprimer.
 *
 * @param string $service
 *        Alias du service.
 *
 * @return array
 * 		Tableau des erreurs qui se limite à la non sélection d'au moins un cache.
 */
function formulaires_vider_cache_rainette_service_verifier($service) {

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
 * @uses cache_supprimer()
 *
 * @param string $service
 *        Alias du service.
 *
 * @return array
 *        Tableau retourné par le formulaire contenant toujours un message de bonne exécution. L'indicateur
 *        editable est toujours à vrai.
 */
function formulaires_vider_cache_rainette_service_traiter($service) {

	$retour = array();

	// On récupère les caches à supprimer
	$caches = _request('caches');

	// On appelle l'API des caches
	include_spip('inc/rainette_normaliser');
	cache_supprimer($service, $caches);

	$retour['message_ok'] = _T('rainette:succes_vider_caches');
	$retour['editable'] = true;

	return $retour;
}
