<?php
/**
 * Ce fichier contient les fonctions de service du plugin Cache.
 *
 * Chaque fonction, soit aiguille, si elle existe, vers une fonction "homonyme" propre au plugin appelant
 * soit déroule sa propre implémentation.
 * Ainsi, les plugins externes peuvent, si elle leur convient, utiliser l'implémentation proposée par Cache Factory.
 *
 * @package SPIP\CACHE\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -----------------------------------------------------------------------
// ---------------------- SERVICES SURCHARGEABLES ------------------------
// -----------------------------------------------------------------------

/**
 * Récupère la configuration des caches d'un plugin, la complète et la stocke dans une meta.
 *
 * Le plugin Cache Factory propose une configuration par défaut des caches.
 *
 * @uses cache_chercher_service()
 * @uses sous_repertoire()
 * @uses lire_config()
 * @uses ecrire_config()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return array
 *        Tableau de la configuration complétée des caches d'un plugin venant d'être enregistrée.
 */
function cache_cache_configurer($plugin) {

	// Initialisation du tableau de configuration avec les valeurs par défaut du plugin Cache.
	$configuration_defaut = array(
		'racine'        => _DIR_CACHE,
		'nom'           => array('nom'),
		'extension'     => '.txt',
		'securisation'  => false,
		'serialisation' => true,
		'separateur'    => '_'
	);

	// Le plugin utilisateur doit fournir un service propre pour la configuration de ses caches.
	// Cette configuration peut-être partielle, dans ce cas les données manquantes sont complétées
	// par celles par défaut.
	$configuration_plugin = array();
	if ($configurer = cache_chercher_service($plugin, 'cache_configurer')) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$configuration_plugin = $configurer($plugin);
	}

	// On merge la configuration du plugin avec celle par défaut pour assure la complétude.
	$configuration = array_merge($configuration_defaut, $configuration_plugin);

	// On vérifie l'indicateur de sécurisation : si le cache doit être sécurisé alors son extension
	// doit absolument être .php. Si ce n'est pas le cas on la force.
	if ($configuration['securisation']
	and ($configuration['extension'] != '.php')) {
		$configuration['extension'] = '.php';
	}

	// Pour faciliter la construction du chemin des caches on détermine une fois pour toute le dossier
	// de base des caches du plugin.
	// -- Vérification de la localisation de la racine qui ne peut être que dans les trois dossiers SPIP
	//    prévus et de la présence du / final.
	if (!in_array($configuration['racine'], array(_DIR_CACHE, _DIR_TMP, _DIR_VAR))) {
		$configuration['racine'] = $configuration_defaut['racine'];
	} else {
		// On s'assure que la racine se termine toujours par un slash.
		$configuration['racine'] = rtrim($configuration['racine'], '/') . '/';
	}
	// -- Sous-dossier spécifique au plugin
	$sous_dossier = ($configuration['racine'] == _DIR_VAR) ? "cache-${plugin}" : "$plugin";
	// -- Création et enregistrement du dossier de base
	include_spip('inc/flock');
	$configuration['dossier_base'] = sous_repertoire($configuration['racine'], $sous_dossier);
	
	// Enregistrement de la configuration du plugin utilisateur dans la meta prévue.
	// Si une configuration existe déjà on l'écrase.
	include_spip('inc/config');
	$meta_cache = lire_config('cache', array());
	$meta_cache[$plugin] = $configuration;
	ecrire_config('cache', $meta_cache);

	return $configuration;
}


/**
 * Construit le chemin complet du fichier cache.
 *
 * @uses cache_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $cache
 *        Tableau identifiant le cache pour lequel on veut construire le nom.
 * @param array  $configuration
 *        Configuration complète des caches du plugin utlisateur lue à partir de la meta de stockage.
 *
 * @return string
 */
function cache_cache_composer($plugin, $cache, $configuration) {

	// Le plugin utilisateur peut fournir un service propre pour construire le chemin complet du fichier cache.
	// Néanmoins, étant donné la généricité du mécanisme offert par le plugin Cache cela devrait être rare.
	if ($composer = cache_chercher_service($plugin, 'cache_composer')) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$fichier_cache = $composer($plugin, $cache, $configuration);
	} else {
		// On utilise le mécanisme de nommage standard du plugin Cache.
		// Initialisation du chemin complet du fichier cache
		$fichier_cache = '';

		// Détermination du répertoire final du fichier cache qui peut-être inclus dans un sous-dossier du dossier
		// de base des caches du plugin.
		$dir_cache = $configuration['dossier_base'];
		if (!empty($cache['sous_dossier'])) {
			// Si le cache nécessite un sous-dossier, appelé service dans l'identifiant du cache.
			$dir_cache .= rtrim($cache['sous_dossier'], '/') . '/';
		}

		// Détermination du nom du cache sans extension.
		// Celui-ci est construit à partir des éléments fournis sur le cache et de la configuration
		// fournie par le plugin (liste ordonnée de composant).
		$nom_cache = '';
		foreach ($configuration['nom'] as $_composant) {
			if (isset($cache[$_composant])) {
				$nom_cache .= ($nom_cache ? $configuration['separateur'] : '') . $cache[$_composant];
			}
		}

		// Si le nom a pu être construit on finalise le chemin complet, sinon on renvoie une chaine vide.
		if ($nom_cache) {
			// L'extension par défaut est dans la configuration mais peut-être forcée pour un cache donné.
			// Par contre, si le cache est sécurisé alors on ne tient pas compte du forçage éventuel car l'extension
			// doit toujours être .php et celle-ci a été forcée lors de la configuration des caches du plugin.
			$extension = (!empty($cache['extension']) and !$configuration['securisation'])
				? $cache['extension']
				: $configuration['extension'];
			// Le chemin complet
			$fichier_cache = "${dir_cache}${nom_cache}${extension}";
		}
	}

	return $fichier_cache;
}


/**
 * Décompose le chemin complet du fichier cache en composants déterminé par configuration.
 *
 * @uses cache_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $fichier_cache
 *        Le chemin complet du fichier à phraser.
 * @param array  $configuration
 *        Configuration complète des caches du plugin utlisateur lue à partir de la meta de stockage.
 *
 * @return array
 *         Tableau des composants constitutifs du cache
 */
function cache_cache_decomposer($plugin, $fichier_cache, $configuration) {

	// Le plugin utilisateur peut fournir un service propre pour construire le chemin complet du fichier cache.
	// Néanmoins, étant donné la généricité du mécanisme offert par le plugin Cache cela devrait être rare.
	if ($decomposer = cache_chercher_service($plugin, 'cache_decomposer')) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$cache = $decomposer($plugin, $cache, $configuration);
	} else {
		// On utilise le mécanisme de nommage standard du plugin Cache. De fait, on considère qu'aucun composant
		// n'est facultatif ou du moins qu'un seul composant est facultatif et positionné en dernier.

		// Initialisation du tableau cache
		$cache = array();

		// On supprime le dossier de base pour n'avoir que la partie spécifique du cache.
		$fichier_cache = str_replace($configuration['dossier_base'], '', $fichier_cache);

		// Détermination du nom du cache sans extension et décomposition suivant la configuration du nom.		
		$nom_cache = basename($fichier_cache, $configuration['extension']);
		foreach (explode($configuration['separateur'], $nom_cache) as $_cle => $_composant) {
			$cache[$configuration['nom'][$_cle]] = $_composant;
		}

		// Identification d'un sous-dossier si il existe.
		if ($sous_dossier = dirname($fichier_cache)) {
			$cache['sous_dossier'] = $sous_dossier;
		}
	}

	return $cache;
}


// -----------------------------------------------------------------------
// ----------------- UTILITAIRE PROPRE AU PLUGIN CACHE -------------------
// -----------------------------------------------------------------------

/**
 * Cherche une fonction donnée en se basant sur le plugin appelant.
 * Si le plugin utilisateur ne fournit pas la fonction demandée la chaîne vide est renvoyée.
 *
 * @internal
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param bool   $fonction
 *        Nom de la fonction de service à chercher.
 *
 * @return string
 *        Nom complet de la fonction si trouvée ou chaine vide sinon.
 */
function cache_chercher_service($plugin, $fonction) {

	$fonction_trouvee = '';

	// Eviter la réentrance si on demande explicitement le service du plugin Cache.
	if ($plugin != 'cache') {
		include_spip("cache/${plugin}");
		$fonction_trouvee = "${plugin}_${fonction}";
		if (!function_exists($fonction_trouvee)) {
			$fonction_trouvee = '';
		}
	}

	return $fonction_trouvee;
}