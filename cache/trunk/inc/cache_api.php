<?php
/**
 * Ce fichier contient les fonctions d'API de gestion des caches.
 *
 * @package SPIP\CACHE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ecrit un contenu dans le cache spécifié d'un plugin utilisateur.
 *
 * @api
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Nom et extension du fichier cache.
 * @param array  $contenu
 *        Contenu sous forme de tableau à stocker dans un fichier cache après sérialisation.
 *
 * @return bool
 */
function cache_ecrire($plugin, $conteneur, $contenu) {

	// Initialisation du retour de la fonction
	$cache_ecrit = false;
	
	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel).
	static $configuration = array();
	if (!$configuration and (!$configuration = cache_configuration_lire($plugin))) {
		$configuration = cache_cache_configurer($plugin);
	}
	
	// Création du répertoire du cache à créer, si besoin.
	if (!empty($conteneur['sous_dossier'])) {
		// Si le conteneur nécessite un sous-dossier, appelé service dans l'identifiant du conteneur.
		sous_repertoire($configuration['dossier_base'], rtrim($conteneur['sous_dossier'], '/'));
	}

	// Détermination du chemin du cache :
	// - le nom sans extension est construit à partir des éléments fournis sur le conteneur et
	//   de la configuration du nom pour le plugin.
	if ($fichier_cache = cache_nommer($plugin, $conteneur, $configuration)) {
 		// Suivant que la configuration demande un sérialisation ou pas, on vérife le format du contenu
		// de façon à toujours écrire une chaine.
		$contenu_cache = '';
		if ($configuration['serialisation']) {
			if (!is_array($contenu)) {
				$contenu_cache = $contenu_cache ? array($contenu_cache) : array();
			}
			$contenu_cache = serialize($contenu_cache);
		} else {
			if (is_string($contenu)) {
				$contenu_cache = $contenu;
			}
		}

		// Ecriture du fichier cache sécurisé ou pas suivant la configuration.
		include_spip('inc/flock');
		$ecrire = 'ecrire_fichier';
		if ($configuration['securisation']) {
			$ecrire = 'ecrire_fichier_securise';
		}
		$cache_ecrit = $ecrire($fichier_cache, $contenu_cache);
	}
	
	return $cache_ecrit;
}


/**
 * Lit la configuration des caches d'un plugin utilisateur.
 *
 * @api
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return array
 *        Tableau de configuration des caches d'un plugin utilisateur ou tableau vide si aucune configuration n'est encore
 *        enregistrée.
 */
function cache_configuration_lire($plugin) {

	// Initialisation de la configuration à retourner
	$configuration_lue = array();

	if ($plugin) {
		// Récupération de la meta du plugin Cache
		include_spip('inc/config');
		$configuration_lue = lire_config("cache/${plugin}", array());
	}

	return $configuration_lue;
}


/**
 * Lit le cache spécifié d'un plugin donné et renvoie le contenu sous forme de tableau
 * éventuellement vide.
 *
 * @api
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $nom_cache
 *        Nom et extension du fichier cache.
 *
 * @return array|string|bool
 *        Contenu du fichier sous la forme d'un tableau, d'une chaine ou false si une erreur s'est produite.
 */
function cache_lire($plugin, $conteneur) {

	// Initialisation du contenu du cache
	$cache_lu = false;
	
	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel, peu probable pour une lecture).
	static $configuration = array();
	if (!$configuration and (!$configuration = cache_configuration_lire($plugin))) {
		$configuration = cache_cache_configurer($plugin);
	}

	// Détermination du nom du cache en fonction du plugin appelant et du type
	if ($fichier_cache = cache_nommer($plugin, $conteneur, $configuration)) {
		// Lecture du fichier cache sécurisé ou pas suivant la configuration.
		include_spip('inc/flock');
		$lire = 'lire_fichier';
		if ($configuration['securisation']) {
			$lire = 'lire_fichier_securise';
		}
		$contenu_cache = '';
		$lecture_ok = $lire($fichier_cache, $contenu_cache);
		
		if ($lecture_ok) {
			if ($configuration['serialisation']) {
				$cache_lu = unserialize($contenu_cache);
			} else {
				$cache_lu = $contenu_cache;
			}
		}
	}

	return $cache_lu;
}


/**
 * Renvoie le chemin complet du cache si celui-ci existe sinon renvoie une chaine vide.
 *
 * @api
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau identifiant le cache pour lequel on veut construire le nom.
 * @param array  $configuration
 *        Configuration complète des caches du plugin utlisateur.
 *
 * @return string
 */
function cache_existe($plugin, $conteneur) {

	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel, peu probable pour cette fonction).
	static $configuration = array();
	if (!$configuration and (!$configuration = cache_configuration_lire($plugin))) {
		$configuration = cache_cache_configurer($plugin);
	}

	// Détermination du nom du cache en fonction du plugin appelant et du type
	if ($fichier_cache = cache_nommer($plugin, $conteneur, $configuration)) {
		// Vérifier l'existence du fichier.
		if (!file_exists($fichier_cache)) {
			$fichier_cache = '';
		}
	} else {
		$fichier_cache = '';
	}

	return $fichier_cache;
}


/**
 * Supprime le cache spécifié d'un plugin donné.
 *
 * @api
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $nom_cache
 *        Nom et extension du fichier cache.
 *
 * @return void
 */
function cache_vider($plugin, $caches = array()) {

	// Détermination du nom du cache en fonction du plugin appelant et du type
	$fichier_cache = '';

	// Suppression du fichier cache
	include_spip('inc/flock');
	supprimer_fichier($fichier_cache);
}
