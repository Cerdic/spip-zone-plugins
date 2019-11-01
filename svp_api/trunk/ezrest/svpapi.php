<?php
/**
 * Ce fichier contient l'ensemble des fonctions de service spécifiques à une ou plusieurs collections.
 *
 * @package SPIP\SVPAPI\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -----------------------------------------------------------------------
// ---------------------------- CONTEXTE ---------------------------------
// -----------------------------------------------------------------------
/**
 * Détermine si le serveur est capable de répondre aux requêtes SVP.
 * Pour cela on vérifie si le serveur est en mode run-time ou pas.
 * On considère qu'un serveur en mode run-time n'est pas valide pour
 * traiter les requêtes car la liste des plugins et des paquets n'est
 * pas complète.
 *
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont uniquement les suivants car les autres sont initialisés par l'appelant :
 *        - `type`    : identifiant de l'erreur 501, soit `runtime_nok`
 *        - `element` : type d'objet sur lequel porte l'erreur, soit `serveur`
 *        - `valeur`  : la valeur du mode runtime
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function svpapi_api_verifier_contexte(&$erreur) {

	// Initialise le retour à true par défaut.
	$est_valide = true;

	include_spip('inc/svp_phraser');
	include_spip('inc/config');
	$mode = lire_config("svp/mode_runtime", 'non');
	if (_SVP_MODE_RUNTIME
	or (!_SVP_MODE_RUNTIME and ($mode == 'oui'))) {
		$erreur['type'] = 'runtime_nok';
		$erreur['element'] = 'svp_mode_runtime';
		$erreur['valeur'] = _SVP_MODE_RUNTIME;

		$est_valide = false;
	}

	return $est_valide;
}

/**
 * Compléte le bloc d'information du plugin en supprimant le schéma du plugin SVP API qui n'existe pas par celui
 * su plugin SVP sur lequel s'appuie SVP API.
 *
 * @param array $contenu Le contenu de la réponse dans son état après initialisation.
 *
 * @return array Le contenu de la réponse avec l'index `schema` supprimé et remplacé par l'index `schema_svp`.
 */
function svpapi_reponse_informer_plugin($contenu) {

	// On met à jour les informations sur le plugin utilisateur maintenant qu'il est connu.
	// -- Récupération du schéma de données et de la version du plugin.
	include_spip('inc/filtres');
	$informer = charger_filtre('info_plugin');
	$schema = $informer('svp', 'schema', true);

	$contenu['fournisseur']['schema'] = "${schema} (SVP)";

	return $contenu;
}


// -----------------------------------------------------------------------
// ----------------------------- PLUGINS ---------------------------------
// -----------------------------------------------------------------------

/**
 * Récupère la liste des plugins de la table spip_plugins éventuellement filtrés par les critères
 * additionnels positionnés dans la requête.
 * Les plugins fournis sont toujours issus d'un dépôt hébergé par le serveur ce qui exclu les plugins
 * installés sur le serveur et non liés à un dépôt (par exemple un zip personnel).
 * Chaque objet plugin est présenté comme un tableau dont tous les champs sont accessibles comme un
 * type PHP simple, entier, chaine ou tableau.
 *
 * @uses plugin_normaliser_champs()
 *
 * @param array $filtres
 *      Tableau des critères de filtrage additionnels à appliquer au select.
 * @param array $configuration
 *      Configuration de la collection plugins utile pour savoir quelle fonction appeler pour construire chaque filtre.
 *
 * @return array
 *      Tableau des plugins dont l'index est le préfixe du plugin.
 *      Les champs de type id ou maj ne sont pas renvoyés.
 */
function plugins_collectionner($filtres, $configuration) {

	// Initialisation de la collection
	$plugins = array();

	// Récupérer la liste des plugins (filtrée ou pas).
	// -- Les plugins appartiennent forcément à un dépot logique installés sur le serveur. Les plugins
	//    installés directement sur le serveur, donc hors dépôt sont exclus.
	$from = array('spip_plugins', 'spip_depots_plugins');
	$group_by = array('spip_plugins.id_plugin');
	// -- Tous le champs sauf id_plugin et id_depot.
	$description_table = lister_tables_objets_sql('spip_plugins');
	$select = array_keys($description_table['field']);
	$select = array_diff($select, array('id_depot', 'id_plugin'));

	// -- Initialisation du where avec les conditions sur la table des dépots.
	$where = array('spip_depots_plugins.id_depot>0', 'spip_depots_plugins.id_plugin=spip_plugins.id_plugin');
	// -- Si il y a des critères additionnels on complète le where en conséquence en fonction de la configuration.
	if ($filtres) {
		// Extraire la configuration des critères
		$criteres = array_column($configuration['filtres'], null, 'critere');
		foreach ($filtres as $_critere => $_valeur) {
			if ($_critere == 'compatible_spip') {
				$filtrer = charger_fonction('where_compatible_spip', 'inc');
				$where[] = $filtrer($_valeur, 'spip_plugins', '>');
			} else {
				// On regarde si il y une fonction particulière permettant le calcul du critère ou si celui-ci
				// est calculé de façon standard.
				$module = !empty($criteres[$_critere]['module'])
					? $criteres[$_critere]['module']
					: $configuration['module'];
				include_spip("svpapi/${module}");
				$construire = "plugins_construire_critere_${_critere}";
				if (function_exists($construire)) {
					$where[] = $construire($_valeur);
				} else {
					$where[] = "spip_plugins.${_critere}=" . sql_quote($_valeur);
				}
			}
		}
	}

	$collection = sql_allfetsel($select, $from, $where, $group_by);

	// On refactore le tableau de sortie du allfetsel en un tableau associatif indexé par les préfixes.
	// On transforme les champs multi en tableau associatif indexé par la langue et on désérialise les
	// champs sérialisés.
	if ($collection) {
		include_spip('inc/svpapi_plugin');
		foreach ($collection as $_plugin) {
			$plugins[$_plugin['prefixe']] = plugin_normaliser_champs('plugin', $_plugin);
		}
	}

	return $plugins;
}


/**
 * Retourne la description complète d'un plugin et de ses paquets.
 *
 * @param string $prefixe
 *        La valeur du préfixe du plugin.
 *
 * @return array
 *         La description du plugin et de ses paquets, les champs étant tous normalisés (désérialisés).
 */
function plugins_ressourcer($prefixe) {

	// Initialisation du tableau de la ressource
	$ressource = array();

	// On recherche d'abord le plugin par son préfixe dans la table spip_plugins.
	// -- Acquisition du plugin (on est sur qu'il est en base) et suppression de l'id qui est inutile.
	include_spip('inc/svp_plugin');
	$plugin = plugin_lire($prefixe);
	unset($plugin['id_plugin']);
	// -- Normalisation des champs.
	include_spip('inc/svpapi_plugin');
	$ressource['plugin'] = plugin_normaliser_champs('plugin', $plugin);

	// On recherche maintenant les paquets du plugin.
	$from = array('spip_paquets');

	// -- Tous le champs sauf id_plugin et id_depot.
	$description_table = lister_tables_objets_sql('spip_paquets');
	$select = array_keys($description_table['field']);
	$champs_inutiles = array(
		'id_paquet', 'id_plugin', 'id_depot',
		'actif', 'installe', 'recent', 'maj_version', 'superieur', 'obsolete', 'attente', 'constante', 'signature'
	);
	$select = array_diff($select, $champs_inutiles);

	// -- Préfixe et conditions sur le dépôt pour exclure les paquets installés.
	$where = array(
		'prefixe=' . sql_quote(strtoupper($prefixe)),
		'id_depot>0'
	);

	// Acquisition des paquets et normalisation des champs.
	$ressource['paquets'] = array();
	$paquets = sql_allfetsel($select, $from, $where);
	if ($paquets) {
		// On refactore en un tableau associatif indexé par archives zip.
		foreach ($paquets as $_paquet) {
			$ressource['paquets'][$_paquet['nom_archive']] = plugin_normaliser_champs('paquet', $_paquet);
		}
	}

	return $ressource;
}


/**
 * Détermine si la valeur du critère compatibilité SPIP est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec
 * un numéro de version ou de branche.
 *
 * @param string $valeur
 *        La valeur du critère compatibilite SPIP
 * @param string $extra
 *        Message complémentaire à renvoyer dans la réponse en cas d'erreur.
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function plugins_verifier_filtre_compatible_spip($valeur, &$erreur) {

	$est_valide = true;

	if (!preg_match('#^((?:\d+)(?:\.\d+){0,2})(?:,(\d+\.\d+)){0,}$#', $valeur)) {
		$est_valide = false;
		$erreur['type'] = 'critere_compatible_spip_nok';
	}

	return $est_valide;
}


/**
 * Détermine si la valeur du préfixe de plugin est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec
 * celui d'un nom de variable.
 *
 * @param string $prefixe
 *        La valeur du préfixe
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function plugins_verifier_ressource_prefixe($prefixe) {

	$est_valide = true;

	// On teste en premier si le préfixe est syntaxiquement correct pour éviter un accès SQL dans ce cas.
	if (!preg_match('#^(\w){2,}$#', strtolower($prefixe))) {
		$est_valide = false;
	} else {
		// On vérifie ensuite si la ressource est bien un plugin fourni par un dépôt
		// et pas un plugin installé sur le serveur uniquement.
		include_spip('inc/svp_plugin');
		if (!plugin_lire($prefixe)) {
			$est_valide = false;
		}
	}

	return $est_valide;
}


// -----------------------------------------------------------------------
// ----------------------------- DEPOTS ----------------------------------
// -----------------------------------------------------------------------

/**
 * Récupère la liste des dépôts hébergés par le serveur.
 * Contrairement aux plugins et paquets les champs d'un dépôt ne nécessitent aucun formatage.
 *
 * @param array $filtres
 *      Tableau des critères additionnels à appliquer au select (non utilisé).
 * @param array $configuration
 *      Configuration de la collection dépôts utile pour savoir quelle fonction appeler pour construire chaque filtre.
 *
 * @return array
 *      Tableau des dépôts.
 *      Les champs de type id ou maj ne sont pas renvoyés.
 */
function depots_collectionner($filtres, $configuration) {

	// Récupérer la liste des dépôts (filtrée ou pas).
	include_spip('base/objets');
	$from = array('spip_depots');
	// -- Tous le champs sauf maj et id_depot.
	$description_table = lister_tables_objets_sql('spip_depots');
	$select = array_keys($description_table['field']);
	$select = array_diff($select, array('id_depot', 'maj'));

	// -- Initialisation du where avec les conditions sur la table des dépots.
	$where = array();
	// -- Si il y a des critères additionnels on complète le where en conséquence.
	if ($filtres) {
		// Extraire la configuration des critères
		$criteres = array_column($configuration['filtres'], null, 'critere');
		foreach ($filtres as $_critere => $_valeur) {
			// On regarde si il y une fonction particulière permettant le calcul du critère ou si celui-ci
			// est calculé de façon standard.
			$module = !empty($criteres[$_critere]['module'])
				? $criteres[$_critere]['module']
				: $configuration['module'];
			include_spip("svpapi/${module}");
			$construire = "depots_construire_critere_${_critere}";
			if (function_exists($construire)) {
				$where[] = $construire($_valeur);
			} else {
				$where[] = "spip_plugins.${_critere}=" . sql_quote($_valeur);
			}
		}
	}

	$depots = sql_allfetsel($select, $from, $where);

	return $depots;
}
