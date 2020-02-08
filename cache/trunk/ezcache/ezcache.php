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
 * @uses cache_service_chercher()
 * @uses lire_config()
 * @uses ecrire_config()
 *
 * @param string $plugin Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *                       un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return array Tableau de la configuration complétée des caches d'un plugin venant d'être enregistrée.
 */
function ezcache_cache_configurer($plugin) {

	// Initialisation du tableau de configuration avec les valeurs par défaut du plugin Cache.
	$configuration_defaut = array(
		'racine'          => '_DIR_CACHE', // Emplacement de base du répertoire des caches. Attention c'est la chaine de la constante SPIP
		'sous_dossier'    => false,        // Indicateur d'utilisation d'un sous-dossier
		'nom_obligatoire' => array('nom'), // Composants obligatoires ordonnés de gauche à droite.
		'nom_facultatif'  => array(),      // Composants facultatifs
		'separateur'      => '',           // Caractère de séparation des composants du nom '_' ou '-' ou '' si un seul composant est utilisé
		'extension'       => '.txt',       // Extension du fichier cache (vaut .php si cache sécurisé)
		'securisation'    => false,        // Indicateur de sécurisation du fichier
		'serialisation'   => true,         // Indicateur de sérialisation
		'decodage'        => false,        // Permet d'appliquer une fonction de décodage à la lecture qui dépend de l'extension
		'conservation'    => 0             // Durée de conservation du cache en secondes. 0 pour permanent
	);

	// Le plugin utilisateur doit fournir un service propre pour la configuration de ses caches.
	// Cette configuration peut-être partielle, dans ce cas les données manquantes sont complétées
	// par celles par défaut.
	$configuration_plugin = array();
	if ($configurer = cache_service_chercher($plugin, 'cache_configurer')) {
		$configuration_plugin = $configurer($plugin);
	}

	// On merge la configuration du plugin avec celle par défaut pour assure la complétude.
	$configuration = array_merge($configuration_defaut, $configuration_plugin);

	// On vérifie que la durée de conservation du cache est bien un entier supérieur ou égal à 0.
	// La durée est exprimée en secondes.
	$configuration['conservation'] = abs(intval($configuration['conservation']));

	// On vérifie en priorité la sécurisation. Si le cache doit être sécurisé :
	// - le décodage n'est pas possible
	// - l'extension du cache doit absolument être .php. Si ce n'est pas le cas on la force.
	if ($configuration['securisation']) {
		$configuration['decodage'] = false;
		if ($configuration['extension'] != '.php') {
			$configuration['extension'] = '.php';
		}
	}

	// On vérifie ensuite la sérialisation. Si le cache est sérialisé :
	// - le décodage n'est pas possible.
	if ($configuration['serialisation']) {
		$configuration['decodage'] = false;
	}

	// On vérifie en dernier le décodage. Si le cache demande un décodage :
	// - sécurisation et sérialisation ne sont pas possibles mais ont été traitées précédemment
	// - le cache n'accepte que les extensions : json, xml ou yaml.
	if ($configuration['decodage']) {
		if ((($configuration['extension'] == 'yaml') or ($configuration['extension'] == 'yml'))
		and (!defined('_DIR_PLUGIN_YAML'))) {
			$configuration['decodage'] = false;
		}
	}

	// Pour faciliter la construction du chemin des caches on stocke les éléments récurrents composant
	// le dossier de base.
	// -- Vérification de la localisation de la racine qui ne peut être que dans les trois dossiers SPIP
	//    prévus.
	if (!in_array($configuration['racine'], array('_DIR_CACHE', '_DIR_TMP', '_DIR_VAR'))) {
		$configuration['racine'] = $configuration_defaut['racine'];
	}
	// -- Sous-dossier spécifique au plugin
	$configuration['dossier_plugin'] = ($configuration['racine'] == '_DIR_VAR') ? "cache-${plugin}/" : "${plugin}/";

	// Construction du tableau des composants du nom : dans l'ordre on a toujours les composants obligatoires
	// suivis des composants facultatifs.
	$configuration['nom'] = array_merge($configuration['nom_obligatoire'], $configuration['nom_facultatif']);

	// Si le nom ne comporte qu'un seul composant forcer le séparateur à '' pour ne pas interdire d'utiliser les
	// caractères '_' ou '-' dans le composant unique.
	if (count($configuration['nom']) == 1) {
		$configuration['separateur'] = '';
	}

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
 * @uses cache_service_chercher()
 *
 * @param string $plugin        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *                              ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $cache         Tableau identifiant le cache pour lequel on veut construire le nom.
 * @param array  $configuration Configuration complète des caches du plugin utlisateur lue à partir de la meta de stockage.
 *
 * @return string
 */
function ezcache_cache_composer($plugin, $cache, $configuration) {

	// Le plugin utilisateur peut fournir un service propre pour construire le chemin complet du fichier cache.
	// Néanmoins, étant donné la généricité du mécanisme offert par le plugin Cache cela devrait être rare.
	if ($composer = cache_service_chercher($plugin, 'cache_composer')) {
		$fichier_cache = $composer($plugin, $cache, $configuration);
	} else {
		// On utilise le mécanisme de nommage standard du plugin Cache.
		// Initialisation du chemin complet du fichier cache
		$fichier_cache = '';

		// Détermination du répertoire final du fichier cache qui peut-être inclus dans un sous-dossier du dossier
		// de base des caches du plugin.
		$dir_cache = constant($configuration['racine']) . $configuration['dossier_plugin'];
		if ($configuration['sous_dossier']) {
			if (!empty($cache['sous_dossier'])) {
				// Si le cache nécessite un sous-dossier, appelé sous_dossier dans l'identifiant du cache.
				$dir_cache .= rtrim($cache['sous_dossier'], '/') . '/';
			} else {
				// C'est une erreur, le sous-dossier n'a pas été fourni alors qu'il est requis.
				$dir_cache = '';
			}
		}

		// Détermination du nom du cache sans extension.
		// Celui-ci est construit à partir des éléments fournis sur le cache et de la configuration
		// fournie par le plugin (liste ordonnée de composant).
		$nom_cache = '';
		if ($dir_cache) {
			foreach ($configuration['nom'] as $_composant) {
				if (isset($cache[$_composant])) {
					if (!$nom_cache) {
						// Il y a forcément un composant non vide en premier.
						$nom_cache .= $cache[$_composant];
					} elseif ($cache[$_composant]
						or (!$cache[$_composant] and in_array($_composant, $configuration['nom_obligatoire']))) {
						// Le composant est à ajouter : non vide ou vide mais obligatoire (cas bizarre!)
						$nom_cache .= $configuration['separateur'] . $cache[$_composant];
					}
				}
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
 * Décompose le chemin complet du fichier cache en éléments constitutifs. Par défaut, le tableau obtenu coïncide
 * avec l’identifiant relatif du cache. La fonction utilise la configuration générale pour connaitre la structure
 * du chemin du fichier.
 *
 * Cache Factory renvoie uniquement les éléments de l'identifiant relatif.
 *
 * @uses cache_service_chercher()
 *
 * @param string $plugin        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *                              ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $fichier_cache Le chemin complet du fichier à phraser.
 * @param array  $configuration Configuration complète des caches du plugin utlisateur lue à partir de la meta de stockage.
 *
 * @return array Tableau des composants constitutifs du cache
 */
function ezcache_cache_decomposer($plugin, $fichier_cache, $configuration) {

	// Le plugin utilisateur peut fournir un service propre pour construire le chemin complet du fichier cache.
	// Néanmoins, étant donné la généricité du mécanisme offert par le plugin Cache cela devrait être rare.
	if ($decomposer = cache_service_chercher($plugin, 'cache_decomposer')) {
		$cache = $decomposer($plugin, $fichier_cache, $configuration);
	} else {
		// On utilise le mécanisme de nommage standard du plugin Cache. De fait, on considère qu'aucun composant
		// n'est facultatif ou du moins qu'un seul composant est facultatif et positionné en dernier.

		// Initialisation du tableau cache
		$cache = array();

		// On supprime le dossier de base pour n'avoir que la partie spécifique du cache.
		$dir_cache = constant($configuration['racine']) . $configuration['dossier_plugin'];
		$fichier_cache = str_replace($dir_cache, '', $fichier_cache);

		// Détermination du nom du cache sans extension et décomposition suivant la configuration du nom.
		$nom_cache = basename($fichier_cache, $configuration['extension']);
		if (count($configuration['nom']) == 1) {
			// Le nom est composé d'un seul composant : on le renvoie directement.
			$cache[$configuration['nom'][0]] = $nom_cache;
		} else {
			// Le nom est composé de plus d'un composant.
			foreach (explode($configuration['separateur'], $nom_cache) as $_cle => $_composant) {
				$cache[$configuration['nom'][$_cle]] = $_composant;
			}
		}

		// Identification d'un sous-dossier si il existe.
		if ($configuration['sous_dossier'] and ($sous_dossier = dirname($fichier_cache))) {
			$cache['sous_dossier'] = $sous_dossier;
		}
	}

	return $cache;
}

/**
 * Complète la description d'un cache issue du service `cache_decomposer()`.
 *
 * Le plugin Cache Factory complète la description canonique avec le nom sans extension et l'extension du fichier.
 *
 * @uses cache_service_chercher()
 *
 * @param string $plugin        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *                              ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $cache         Tableau identifiant le cache pour lequel on veut construire le nom.
 * @param string $fichier_cache Fichier cache désigné par son chemin complet.
 * @param array  $configuration Configuration complète des caches du plugin utilisateur lue à partir de la meta de stockage.
 *
 * @return array Description du cache complétée par un ensemble de données propres au plugin.
 */
function ezcache_cache_completer($plugin, $cache, $fichier_cache, $configuration) {

	// Cache Factory complète la description avec le nom sans extension et l'extension du fichier cache avant
	// de passer la main au plugin utilisateur.
	$cache['nom_cache'] = basename($fichier_cache, $configuration['extension']);
	$cache['extension_cache'] = $configuration['extension'];

	// Le plugin utilisateur peut fournir un service propre pour construire le chemin complet du fichier cache.
	// Néanmoins, étant donné la généricité du mécanisme offert par le plugin Cache cela devrait être rare.
	if ($completer = cache_service_chercher($plugin, 'cache_completer')) {
		$cache = $completer($plugin, $cache, $fichier_cache, $configuration);
	}

	return $cache;
}

/**
 * Décode le contenu du fichier cache en fonction de l'extension.
 *
 * Le plugin Cache Factory utilise des fonctions standard de PHP, SPIP ou du plugin YAML. Un plugin appelant peut
 * proposer une fonction spécifique de décodage
 *
 * @uses cache_service_chercher()
 *
 * @param string $plugin        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *                              ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $contenu       Contenu du fichier cache au format chaine.
 * @param array  $configuration Configuration complète des caches du plugin utilisateur lue à partir de la meta de stockage.
 *
 * @return array Contenu du cache décodé si la fonction idoine a été appliqué ou tel que fourni en entrée sinon.
 */
function ezcache_cache_decoder($plugin, $contenu, $configuration) {

	// Cache Factory décode le contenu du fichier cache en fonction de l'extension (json, yaml, yml ou xml).
	$encodage = ltrim($configuration['extension'], '.');

	// Le plugin utilisateur peut fournir un service propre pour décoder le contenu du cache.
	// Néanmoins, étant donné la généricité du mécanisme offert par le plugin Cache cela devrait être rare.
	if ($decoder = cache_service_chercher($plugin, "cache_decoder_${encodage}")) {
		$contenu = $decoder($plugin, $contenu);
	} else {
		// Utilisation des fonctions génériques de Cache Factory
		switch ($encodage) {
			case 'json':
				// On utilise la fonction PHP native
				$contenu = json_decode($contenu, true);
				break;
			case 'yaml':
			case 'yml':
				// On utilise la fonction du plugin YAML si il est actif (un jour on l'aura dans SPIP...)
				if (!defined('_DIR_PLUGIN_YAML')) {
					include_spip('inc/yaml');
					$contenu = yaml_decode($contenu);
				}
				break;
			case 'xml':
				// On utilise la fonction historique de SPIP sachant qu'il en existe d'autre. Pour changer il suffit
				// d'utiliser une fonction spécifique du plugin appelant.
				include_spip('inc/xml');
				$contenu = spip_xml_parse($contenu, true);
				break;
			default:
		}
	}

	return $contenu;
}

/**
 * Effectue le chargement du formulaire de vidage des caches pour un plugin utilisateur donné.
 *
 * Le plugin Cache Factory propose une version simplifié du formulaire où tous les fichiers caches
 * sont listées par ordre alphabétique sans possibilité de regroupement.
 *
 * @uses cache_service_chercher()
 * @uses cache_repertorier()
 *
 * @param string $plugin        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *                              ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $options       Tableau d'options qui peut être fourni par un plugin utilisateur uniquement si celui-ci fait appel
 *                              au formulaire. La page cache_vider de Cache Factory n'utilise pas ce paramètre.
 *                              Le tableau est passé à la fonction de service de chargement du formulaire uniquement.
 * @param array  $configuration Configuration complète des caches du plugin utilisateur lue à partir de la meta de stockage.
 *
 * @return array Description du cache complétée par un ensemble de données propres au plugin.
 */
function ezcache_cache_formulaire_charger($plugin, $options, $configuration) {

	// Stocker le préfixe et le nom du plugin de façon systématique.
	$valeurs = array('_prefixe' => $plugin);
	$informer = chercher_filtre('info_plugin');
	$valeurs['_nom_plugin'] = $informer($plugin, 'nom', true);

	// Le plugin utilisateur peut fournir un service propre pour construire le tableau des valeurs du formulaire.
	if ($charger = cache_service_chercher($plugin, 'cache_formulaire_charger')) {
		$valeurs_plugin = $charger($plugin, $options, $configuration);
		if ($valeurs_plugin) {
			$valeurs = array_merge($valeurs, $valeurs_plugin);
		}
	} else {
		// On présente simplement les fichiers caches en ordre alphabétique en visualisant uniquement
		// le sous-dossuer éventuel et le nom du fichier sans décomposition.
		$valeurs['_caches'] = cache_repertorier($plugin, array());
	}

	return $valeurs;
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
 * @param string $plugin   Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *                         un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param bool   $fonction Nom de la fonction de service à chercher.
 *
 * @return string Nom complet de la fonction si trouvée ou chaine vide sinon.
 */
function cache_service_chercher($plugin, $fonction) {
	$fonction_trouvee = '';

	// Eviter la réentrance si on demande explicitement le service du plugin Cache Factory.
	if ($plugin != 'cache') {
		include_spip("ezcache/${plugin}");
		$fonction_trouvee = "${plugin}_${fonction}";
		if (!function_exists($fonction_trouvee)) {
			$fonction_trouvee = '';
		}
	}

	return $fonction_trouvee;
}
