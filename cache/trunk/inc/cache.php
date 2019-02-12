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
 * @uses cache_obtenir_configuration()
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
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_obtenir_configuration($plugin))) {
		$configuration[$plugin] = cache_cache_configurer($plugin);
	}
	
	// Le cache peut-être fourni soit sous la forme d'un chemin complet soit sous la forme d'un
	// tableau permettant de calculer le chemin complet. On prend en compte ces deux cas.
	$fichier_cache = '';
	include_spip('inc/flock');
	if (is_array($cache)) {
		// Vérification de la conformité entre la configuration et le sous-dossier du cache.
		if (!$configuration[$plugin]['sous_dossier']
		or ($configuration[$plugin]['sous_dossier'] and !empty($cache['sous_dossier']))) {
			// Détermination du chemin du cache si pas d'erreur sur le sous-dossier :
			// - le nom sans extension est construit à partir des éléments fournis sur le conteneur et
			//   de la configuration du nom pour le plugin.
			$fichier_cache = cache_cache_composer($plugin, $cache, $configuration[$plugin]);
		}
	} elseif (is_string($cache)) {
		// Le chemin complet du fichier cache est fourni. Aucune vérification ne peut être faite
		// il faut donc que l'appelant ait utilisé l'API pour calculer le fichier au préalable.
		$fichier_cache = $cache;
	}
	
	if ($fichier_cache) {
		// On crée les répertoires si besoin
		$dir_cache = sous_repertoire(
			constant($configuration[$plugin]['racine']),
			rtrim($configuration[$plugin]['dossier_plugin'], '/')
		);
		if ($configuration[$plugin]['sous_dossier']) {
			sous_repertoire($dir_cache, rtrim($cache['sous_dossier'], '/'));
		}

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
 * @uses cache_obtenir_configuration()
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
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_obtenir_configuration($plugin))) {
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
 * @uses cache_obtenir_configuration()
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
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_obtenir_configuration($plugin))) {
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
 * @uses cache_obtenir_configuration()
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
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_obtenir_configuration($plugin))) {
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
 * @uses cache_obtenir_configuration()
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
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_obtenir_configuration($plugin))) {
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
 * Retourne la description complète des caches d'un plugin filtrés sur une liste de critères.
 *
 * @api
 *
 * @uses cache_obtenir_configuration()
 * @uses cache_cache_configurer()
 * @uses cache_cache_composer()
 * @uses cache_cache_decomposer()
 * @uses cache_cache_completer()
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array $filtres
 *        Tableau associatif `[champ] = valeur` ou `[champ] = !valeur` de critères de filtres sur les composants
 *        de caches. Les opérateurs égalité et inégalité sont possibles.
 *
 * @return array
 *        Tableau des descriptions des fichiers cache créés par le plugin indexé par le chemin complet de
 *        chaque fichier cache.
 */
function cache_repertorier($plugin, $filtres = array()) {

	// Initialisation de la liste des caches
	$caches = array();

	// Lecture de la configuration des caches du plugin.
	// Si celle-ci n'existe pas encore elle est créée (cas d'un premier appel, peu probable pour une lecture).
	static $configuration = array();
	include_spip('cache/cache');
	if (empty($configuration[$plugin]) and (!$configuration[$plugin] = cache_obtenir_configuration($plugin))) {
		$configuration[$plugin] = cache_cache_configurer($plugin);
	}

	// Rechercher les caches du plugin sans appliquer de filtre si ce n'est sur le sous-dossier éventuellement.
	// Les autres filtres seront appliqués sur les fichiers récupérés.
	$pattern_fichier = constant($configuration[$plugin]['racine']) . $configuration[$plugin]['dossier_plugin'];
	if ($configuration[$plugin]['sous_dossier']) {
		if (array_key_exists('sous_dossier', $filtres)) {
			$pattern_fichier .= rtrim($filtres['sous_dossier'], '/') . '/';
		} else {
			$pattern_fichier .= '*/';
		}
	}

	// On complète le pattern avec une recherche d'un nom quelconque mais avec l'extension configurée.
	$pattern_fichier .= '*' . $configuration[$plugin]['extension'];

	// On recherche les fichiers correspondant au pattern.
	$fichiers_cache = glob($pattern_fichier);

	if ($fichiers_cache) {
		foreach ($fichiers_cache as $_fichier_cache) {
			// On décompose le chemin de chaque cache afin de renvoyer l'identifiant canonique du cache.
			$cache = cache_cache_decomposer($plugin, $_fichier_cache, $configuration[$plugin]);

			// Maintenant que les composants sont déterminés on applique les filtres pour savoir si on
			// complète et stocke le cache.
			$cache_conforme = true;
			foreach ($filtres as $_critere => $_valeur) {
				$operateur_egalite = true;
				$valeur = $_valeur;
				if (substr($_valeur, 0, 1) == '!') {
					$operateur_egalite = false;
					$valeur = ltrim($_valeur, '!');
				}
				if (isset($cache[$_critere])
				and (($operateur_egalite and ($cache[$_critere] != $valeur))
					or (!$operateur_egalite and ($cache[$_critere] == $valeur)))) {
					$cache_conforme = false;
					break;
				}
			}

			if ($cache_conforme) {
				// On permet au plugin de completer la description canonique
				$cache = cache_cache_completer($plugin, $cache, $_fichier_cache, $configuration[$plugin]);

				// On stocke la description du fichier cache dans le tableau de sortie.
				$caches[$_fichier_cache] = $cache;
			}
		}
	}

	return $caches;
}


/**
 * Supprime, pour un plugin donné, les caches désignés par leur chemin complet.
 *
 * @api
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $caches
 *        Liste des fichiers caches désignés par leur chemin complet.
 *
 * @return bool
 *         True si la suppression s'est bien passée, false sinon.
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
 * Lit la configuration standard des caches d'un plugin utilisateur ou de tous les plugins utilisateur.
 *
 * @api
 *
 * @uses lire_config()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *        Si vide, toutes les configurations sont fournies.
 *
 * @return array
 *        Tableau de configuration des caches d'un plugin utilisateur ou tableau vide si aucune configuration n'est encore
 *        enregistrée.
 */
function cache_obtenir_configuration($plugin = '') {

	// Retourner la configuration du plugin ou de tous les plugins utilisateur.
	include_spip('inc/config');
	if ($plugin) {
		$configuration_lue = lire_config("cache/${plugin}", array());
	} else {
		$configuration_lue = lire_config('cache', array());
	}

	return $configuration_lue;
}


/**
 * Efface la configuration standard des caches d'un plugin utilisateur ou de tous les plugins utilisateur.
 *
 * @api
 *
 * @uses lire_config()
 * @uses effacer_config()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *        Si vide, toutes les configurations sont effacées.
 *
 * @return bool
 *         True si la suppression s'est bien passée, false sinon.
 */
function cache_effacer_configuration($plugin = '') {

	// Initialisation de la configuration à retourner
	include_spip('inc/config');
	$configuration_effacee = true;

	if ($plugin) {
		// Récupération de la meta du plugin Cache
		$configuration_plugin = lire_config("cache/${plugin}", array());
		if ($configuration_plugin) {
			effacer_config("cache/${plugin}");
		} else {
			$configuration_effacee = false;
		}
	} else {
		effacer_config('cache');
	}

	return $configuration_effacee;
}
