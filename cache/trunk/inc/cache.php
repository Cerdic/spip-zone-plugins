<?php
/**
 * Ce fichier contient les fonctions d'API du plugin Cache Factory.
 *
 * @package SPIP\CACHE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ecrit un contenu dans un cache spécifié par son identifiant.
 *
 * @api
 *
 * @uses cache_configuration_lire()
 * @uses cache_cache_configurer()
 * @uses cache_cache_composer()
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $cache
 *        Identifiant du cache sous la forme d'une chaine (le chemin du fichier) ou d'un tableau fournissant
 *        les composants canoniques du nom.
 * @param array|string $contenu
 *        Contenu sous forme de tableau à sérialiser ou sous la forme d'une chaine.
 *
 * @return bool
 *         True si l'écriture s'est bien passée, false sinon.
 */
function cache_ecrire($plugin, $cache, $contenu) {

	// Initialisation du retour de la fonction
	$cache_ecrit = false;
	
	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel).
	static $configuration = array();
	include_spip('cache/cache');
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_configuration_lire($plugin))) {
		$configuration[$plugin] = cache_cache_configurer($plugin);
	}
	
	// Le cache peut-être fourni soit sous la forme d'un chemin complet soit sous la forme d'un
	// tableau permettant de calculer le chemin complet. On prend en compte ces deux cas.
	$fichier_cache = '';
	include_spip('inc/flock');
	if (is_array($cache)) {
		// Création du répertoire du cache à créer, si besoin.
		if (!empty($cache['sous_dossier'])) {
			// Si le conteneur nécessite un sous-dossier, appelé service dans l'identifiant du conteneur.
			sous_repertoire($configuration[$plugin]['dossier_base'], rtrim($cache['sous_dossier'], '/'));
		}

		// Détermination du chemin du cache :
		// - le nom sans extension est construit à partir des éléments fournis sur le conteneur et
		//   de la configuration du nom pour le plugin.
		$fichier_cache = cache_cache_composer($plugin, $cache, $configuration[$plugin]);
	} elseif (is_string($cache)) {
		// Le chemin complet du fichier cache est fourni. Aucune vérification ne peut être faite
		// il faut donc que l'appelant ait utilisé l'API pour calculer le fichier au préalable.
		$fichier_cache = $cache;
	}
	
	if ($fichier_cache) {
 		// Suivant que la configuration demande une sérialisation ou pas, on vérife le format du contenu
		// de façon à toujours écrire une chaine.
		$contenu_cache = '';
		if ($configuration[$plugin]['serialisation']) {
			if (!is_array($contenu)) {
				$contenu = $contenu ? array($contenu) : array();
			}
			$contenu_cache = serialize($contenu);
		} else {
			if (is_string($contenu)) {
				$contenu_cache = $contenu;
			}
		}

		// Ecriture du fichier cache sécurisé ou pas suivant la configuration.
		$ecrire = 'ecrire_fichier';
		if ($configuration[$plugin]['securisation']) {
			$ecrire = 'ecrire_fichier_securise';
		}
		$cache_ecrit = $ecrire($fichier_cache, $contenu_cache);
	}
	
	return $cache_ecrit;
}


/**
 * Lit le cache spécifié par son identifiant et renvoie le contenu sous forme de tableau
 * ou de chaine éventuellement vide.
 *
 * @api
 *
 * @uses cache_configuration_lire()
 * @uses cache_cache_configurer()
 * @uses cache_cache_composer()
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $cache
 *        Identifiant du cache sous la forme d'une chaine (le chemin du fichier) ou d'un tableau fournissant
 *        les composants canoniques du nom.
 *
 * @return array|string|bool
 *        Contenu du fichier sous la forme d'un tableau, d'une chaine ou false si une erreur s'est produite.
 */
function cache_lire($plugin, $cache) {

	// Initialisation du contenu du cache
	$cache_lu = false;
	
	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel, peu probable pour une lecture).
	static $configuration = array();
	include_spip('cache/cache');
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_configuration_lire($plugin))) {
		$configuration[$plugin] = cache_cache_configurer($plugin);
	}

	// Le cache peut-être fourni soit sous la forme d'un chemin complet soit sous la forme d'un
	// tableau permettant de calculer le chemin complet. On prend en compte ces deux cas.
	$fichier_cache = '';
	if (is_array($cache)) {
		// Détermination du chemin du cache :
		// - le nom sans extension est construit à partir des éléments fournis sur le conteneur et
		//   de la configuration du nom pour le plugin.
		$fichier_cache = cache_cache_composer($plugin, $cache, $configuration[$plugin]);
	} elseif (is_string($cache)) {
		// Le chemin complet du fichier cache est fourni. Aucune vérification ne peut être faite
		// il faut donc que l'appelant ait utilisé l'API pour calculer le fichier au préalable.
		$fichier_cache = $cache;
	}

	// Détermination du nom du cache en fonction du plugin appelant et du type
	if ($fichier_cache) {
		// Lecture du fichier cache sécurisé ou pas suivant la configuration.
		include_spip('inc/flock');
		$lire = 'lire_fichier';
		if ($configuration[$plugin]['securisation']) {
			$lire = 'lire_fichier_securise';
		}
		$contenu_cache = '';
		$lecture_ok = $lire($fichier_cache, $contenu_cache);
		
		if ($lecture_ok) {
			if ($configuration[$plugin]['serialisation']) {
				$cache_lu = unserialize($contenu_cache);
			} else {
				$cache_lu = $contenu_cache;
			}
		}
	}

	return $cache_lu;
}


/**
 * Teste l'existence d'un cache sur le disque et, si il existe, renvoie le chemin complet.
 *
 * @api
 *
 * @uses cache_configuration_lire()
 * @uses cache_cache_configurer()
 * @uses cache_cache_composer()
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $cache
 *        Identifiant du cache sous la forme d'une chaine (le chemin du fichier) ou d'un tableau fournissant
 *        les composants canoniques du nom.
 *
 * @return string
 */
function cache_existe($plugin, $cache) {

	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel).
	static $configuration = array();
	include_spip('cache/cache');
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_configuration_lire($plugin))) {
		$configuration[$plugin] = cache_cache_configurer($plugin);
	}

	// Le cache peut-être fourni soit sous la forme d'un chemin complet soit sous la forme d'un
	// tableau permettant de calculer le chemin complet. On prend en compte ces deux cas.
	$fichier_cache = '';
	if (is_array($cache)) {
		// Détermination du chemin du cache :
		// - le nom sans extension est construit à partir des éléments fournis sur le conteneur et
		//   de la configuration du nom pour le plugin.
		$fichier_cache = cache_cache_composer($plugin, $cache, $configuration[$plugin]);
	} elseif (is_string($cache)) {
		// Le chemin complet du fichier cache est fourni. Aucune vérification ne peut être faite
		// il faut donc que l'appelant ait utilisé l'API cache_existe() pour calculer le fichier au préalable.
		$fichier_cache = $cache;
	}

	// Vérifier l'existence du fichier.
	if ($fichier_cache) {
		if (!file_exists($fichier_cache)) {
			$fichier_cache = '';
		}
	}

	return $fichier_cache;
}


/**
 * Renvoie le chemin complet du cache sans tester son existence.
 * Cette fonction est une encapsulation du service cache_cache_composer().
 *
 * @api
 *
 * @uses cache_configuration_lire()
 * @uses cache_cache_configurer()
 * @uses cache_cache_composer()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $cache
 *        Identifiant du cache sous la forme d'un tableau fournissant les composants canoniques du nom.
 *
 * @return string
 */
function cache_nommer($plugin, $cache) {

	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel).
	static $configuration = array();
	include_spip('cache/cache');
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_configuration_lire($plugin))) {
		$configuration[$plugin] = cache_cache_configurer($plugin);
	}

	// Le cache est toujours fourni sous la forme d'un tableau permettant de calculer le chemin complet.
	$fichier_cache = '';
	if (is_array($cache)) {
		// Détermination du chemin du cache :
		// - le nom sans extension est construit à partir des éléments fournis sur le conteneur et
		//   de la configuration du nom pour le plugin.
		$fichier_cache = cache_cache_composer($plugin, $cache, $configuration[$plugin]);
	}

	return $fichier_cache;
}


/**
 * Supprime le cache spécifié par son identifiant.
 *
 * @api
 *
 * @uses cache_configuration_lire()
 * @uses cache_cache_configurer()
 * @uses cache_cache_composer()
 * @uses supprimer_fichier()
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $cache
 *        Identifiant du cache sous la forme d'une chaine (le chemin du fichier) ou d'un tableau fournissant
 *        les composants canoniques du nom.
 *
 * @return bool
 *         True si la suppression s'est bien passée, false sinon.
 */
function cache_supprimer($plugin, $cache) {

	// Initialisation du contenu du cache
	$cache_supprime = false;
	
	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel, peu probable pour une lecture).
	static $configuration = array();
	include_spip('cache/cache');
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_configuration_lire($plugin))) {
		$configuration[$plugin] = cache_cache_configurer($plugin);
	}

	// Le cache peut-être fourni soit sous la forme d'un chemin complet soit sous la forme d'un
	// tableau permettant de calculer le chemin complet. On prend en compte ces deux cas.
	$fichier_cache = '';
	if (is_array($cache)) {
		// Détermination du chemin du cache :
		// - le nom sans extension est construit à partir des éléments fournis sur le conteneur et
		//   de la configuration du nom pour le plugin.
		$fichier_cache = cache_cache_composer($plugin, $cache, $configuration[$plugin]);
	} elseif (is_string($cache)) {
		// Le chemin complet du fichier cache est fourni. Aucune vérification ne peut être faite
		// il faut donc que l'appelant ait utilisé l'API cache_existe() pour calculer le fichier au préalable.
		$fichier_cache = $cache;
	}

	// Détermination du nom du cache en fonction du plugin appelant et du type
	if ($fichier_cache) {
		// Lecture du fichier cache sécurisé ou pas suivant la configuration.
		include_spip('inc/flock');
		$cache_supprime = supprimer_fichier($fichier_cache);
	}

	return $cache_supprime;
}


/**
 * Supprime le ou les caches spécifiés d'un plugin donné.
 * A AMELIORER
 *
 * @api
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $caches
 *        Liste des fichiers caches (chemin complet) à supprimer.
 *
 * @return bool
 */
function cache_vider($plugin, $caches) {

	// Initialisation du retour
	$cache_vide = false;

	if ($caches) {
		$fichiers_cache = is_string($caches) ? array($caches) : $caches;
		include_spip('inc/flock');
		foreach ($fichiers_cache as $_fichier) {
			supprimer_fichier($_fichier);
		}
		$cache_vide = true;
	}
	
	return $cache_vide;
}


/**
 * Lit la configuration standard des caches d'un plugin utilisateur.
 *
 * @api
 *
 * @uses lire_config()
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
