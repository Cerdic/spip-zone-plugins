<?php
/**
 * Ce fichier contient les fonctions de service du plugin Cache.
 *
 * Chaque fonction, soit aiguille, si elle existe, vers une fonction "homonyme" propre au plugin appelant
 * soit déroule sa propre implémentation.
 * Ainsi, les plugins externes peuvent, si elle leur convient, utiliser l'implémentation proposée par Cache.
 *
 * @package SPIP\CACHE\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_CACHE_RACINE')) {
	/**
	 * Dossier racine dans lesquels tous les caches par défaut seront rangés.
	 * Les caches sont répartis suivant le plugin appelant dans un sous-dossier `/${plugin}`.
	 */
	define('_CACHE_RACINE', _DIR_CACHE);
}

if (!defined('_CACHE_EXTENSION')) {
	/**
	 * Extension par défaut d'un cache.
	 */
	define('_CACHE_EXTENSION', '.txt');
}

if (!defined('_CACHE_SECURISE')) {
	/**
	 * Extension par défaut d'un cache.
	 */
	define('_CACHE_SECURISE', false);
}

if (!defined('_CACHE_CONTENU_SERIALISE')) {
	/**
	 * Extension par défaut d'un cache.
	 */
	define('_CACHE_CONTENU_SERIALISE', true);
}

if (!defined('_CACHE_SEPARATEUR')) {
	/**
	 * Dossier racine dans lesquels tous les caches par défaut seront rangés.
	 * Les caches sont répartis suivant le plugin appelant dans un sous-dossier `/${plugin}`.
	 */
	define('_CACHE_SEPARATEUR', '_');
}


// -----------------------------------------------------------------------
// ---------------------- SERVICES SURCHARGEABLES ------------------------
// -----------------------------------------------------------------------

/**
 * Complète la description d'un type de noisette issue de la lecture de son fichier YAML.
 *
 * Le plugin N-Core ne complète pas les types de noisette.
 *
 * @uses ncore_chercher_service()
 * @uses sous_repertoire()
 * @uses lire_config()
 * @uses ecrire_config()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description du type de noisette issue de la lecture du fichier YAML. Suivant le plugin utilisateur elle
 *        nécessite d'être compléter avant son stockage.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Description du type de noisette éventuellement complétée par le plugin utilisateur.
 */
function cache_cache_configurer($plugin) {

	// Initialisation du tableau de configuration avec les valeurs par défaut du plugin Cache.
	$configuration_defaut = array(
		'racine'        => _CACHE_RACINE,
		'nom'           => array('nom'),
		'extension'     => _CACHE_EXTENSION,
		'securisation'  => _CACHE_SECURISE,
		'serialisation' => _CACHE_CONTENU_SERIALISE,
		'separateur'    => _CACHE_SEPARATEUR
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
		$configuration['racine'] = rtrim($configuration['racine'], '/') . '/';
	}
	// -- Sous-dossier spécifique au plugin
	$sous_dossier = ($configuration['racine'] == _DIR_VAR) ? "cache-${plugin}" : "$plugin";
	// -- Création et enregistrement du dossier de base
	sous_repertoire($configuration['racine'], $sous_dossier);
	$configuration['dossier_base'] = $configuration['racine'] . "${sous_dossier}/";
	
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
function cache_cache_nommer($plugin, $conteneur, $configuration) {

	// Le plugin utilisateur peut fournir un service propre pour construire le chemin complet du fichier cache.
	// Néanmoins, étant donné la généricité du mécanisme offert par le plugin Cache cela devrait être rare.
	if ($nommer = cache_chercher_service($plugin, 'cache_nommer')) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$fichier_cache = $nommer($plugin, $conteneur, $configuration);
	} else {
		// On utilise le mécanisme de nommage standard du plugin Cache.
		// Initialisation du chemin complet du fichier cache
		$fichier_cache = '';

		// Détermination du répertoire final du fichier cache qui peut-être inclus dans un sous-dossier du dossier
		// de base des caches du plugin.
		$dir_cache = $configuration['dossier_base'];
		if (!empty($conteneur['sous_dossier'])) {
			// Si le conteneur nécessite un sous-dossier, appelé service dans l'identifiant du conteneur.
			$dir_cache .= rtrim($conteneur['sous_dossier'], '/');
		}

		// Détermination du nom du cache sans extension.
		// Celui-ci est construit à partir des éléments fournis sur le conteneur et de la configuration
		// fournie par le plugin (liste ordonnée de composant).
		$nom_cache = '';
		foreach ($configuration['nom'] as $_composant) {
			if (isset($conteneur[$_composant])) {
				$nom_cache .= ($nom_cache ? $configuration['separateur'] : '') . $conteneur[$_composant];
			}
		}

		// Si le nom a pu être construit on finalise le chemin complet, sinon on renvoie une chaine vide.
		if ($nom_cache) {
			// L'extension par défaut est dans la configuration mais peut-être forcée pour un cache donné.
			// Par contre, si le cache est sécurisé alors on ne tient pas compte du forçage éventuel car l'extension
			// doit toujours être .php et celle-ci a été forcée lors de la configuration des caches du plugin.
			$extension = (!empty($conteneur['extension']) and !$configuration['securisation'])
				? $conteneur['extension']
				: $configuration['extension'];
			// Le chemin complet
			$fichier_cache = "${dir_cache}${nom_cache}${extension}";
		}
	}

	return $fichier_cache;
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
