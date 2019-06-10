<?php
/**
 * Ce fichier contient l'ensemble des fonctions de service spécifiques à une ou plusieurs collections.
 *
 * @package SPIP\SVPAPI\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Récupère la liste des plugins de la table spip_plugins éventuellement filtrés par les critères
 * additionnels positionnés dans la requête.
 * Les plugins fournis sont toujours issus d'un dépôt hébergé par le serveur ce qui exclu les plugins
 * installés sur le serveur et non liés à un dépôt (par exemple un zip personnel).
 * Chaque objet plugin est présenté comme un tableau dont tous les champs sont accessibles comme un
 * type PHP simple, entier, chaine ou tableau.
 *
 * @uses normaliser_champs()
 *
 * @param array $filtres
 *      Tableau des critères de filtrage additionnels à appliquer au select.
 *
 * @return array
 *      Tableau des plugins dont l'index est le préfixe du plugin.
 *      Les champs de type id ou maj ne sont pas renvoyés.
 */
function plugins_collectionner($filtres) {

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
	// -- Si il y a des critères additionnels on complète le where en conséquence.
	if ($filtres) {
		foreach ($filtres as $_critere => $_valeur) {
			if ($_critere == 'compatible_spip') {
				$filtrer = charger_fonction('where_compatible_spip', 'inc');
				$where[] = $filtrer($_valeur, 'spip_plugins', '>');
			} else {
				$where[] = "spip_plugins.${_critere}=" . sql_quote($_valeur);
			}
		}
	}

	$collection = sql_allfetsel($select, $from, $where, $group_by);

	// On refactore le tableau de sortie du allfetsel en un tableau associatif indexé par les préfixes.
	// On transforme les champs multi en tableau associatif indexé par la langue et on désérialise les
	// champs sérialisés.
	if ($collection) {
		$normaliser = charger_fonction('normaliser_champs', 'inc');
		foreach ($collection as $_plugin) {
			$plugins[$_plugin['prefixe']] = $normaliser('plugin', $_plugin);
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
	// -- Acquisition du plugin (on est sur qu'il est en base).
	$prefixe = strtoupper($prefixe);
	$plugin = plugins_lire_description($prefixe);
	// -- Normalisation des champs.
	$normaliser = charger_fonction('normaliser_champs', 'inc');
	$ressource['plugin'] = $normaliser('plugin', $plugin);

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
			$ressource['paquets'][$_paquet['nom_archive']] = $normaliser('paquet', $_paquet);
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
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function plugins_verifier_critere_compatible_spip($valeur, &$extra) {

	$est_valide = true;

	if (!preg_match('#^((?:\d+)(?:\.\d+){0,2})(?:,(\d+\.\d+)){0,}$#', $valeur)) {
		$est_valide = false;
		$extra = _T('svpapi:extra_critere_compatible_spip');
	}

	return $est_valide;
}


/**
 * Détermine si la valeur du préfixe de plugin est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec
 * celui d'un nom de variable.
 *
 * @param string $valeur
 *        La valeur du préfixe
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function plugins_verifier_ressource_prefixe($valeur) {

	$est_valide = true;

	// On teste en premier si le préfixe est syntaxiquement correct pour éviter un accès SQL dans ce cas.
	if (!preg_match('#^(\w){2,}$#', strtolower($valeur))) {
		$est_valide = false;
	} else {
		// On vérifie ensuite si la ressource est bien un plugin fourni par un dépôt
		// et pas un plugin installé sur le serveur uniquement.
		if (!plugins_lire_description($valeur)) {
			$est_valide = false;
		}
	}

	return $est_valide;
}


/**
 * Retourne la description complète d'un objet plugin identifié par son préfixe.
 *
 * @param $prefixe
 *        La valeur du préfixe du plugin.
 *
 * @return array
 *         La description brute du plugin sans les id.
 */
function plugins_lire_description($prefixe) {

	// Initialisation du tableau de sortie
	static $plugins = array();

	// On passe le préfixe en majuscules pour être cohérent avec le stockage en base.
	$prefixe = strtoupper($prefixe);

	if (!isset($plugins[$prefixe])) {
		// --Initialisation de la jointure entre plugins et dépôts.
		$from = array('spip_plugins', 'spip_depots_plugins');
		$group_by = array('spip_plugins.id_plugin');

		// -- Tous le champs sauf id_plugin et id_depot.
		$description_table = lister_tables_objets_sql('spip_plugins');
		$select = array_keys($description_table['field']);
		$select = array_diff($select, array('id_depot', 'id_plugin'));

		// -- Préfixe, jointure et conditions sur la table des dépots.
		$where = array(
			'spip_plugins.prefixe=' . sql_quote($prefixe),
			'spip_depots_plugins.id_depot>0',
			'spip_depots_plugins.id_plugin=spip_plugins.id_plugin'
		);

		// Acquisition du plugin.
		$plugins[$prefixe] = array();
		if ($plugin = sql_fetsel($select, $from, $where, $group_by)) {
			$plugins[$prefixe] = $plugin;
		}
	}

	return $plugins[$prefixe];
}


/**
 * Récupère la liste des dépôts hébergés par le serveur.
 * Contrairement aux plugins et paquets les champs d'un dépôt ne nécessitent aucun formatage.
 *
 * @param array $filtres
 *      Tableau des critères additionnels à appliquer au select (non utilisé).
 *
 * @return array
 *      Tableau des dépôts.
 *      Les champs de type id ou maj ne sont pas renvoyés.
 */
function depots_collectionner($filtres) {

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
		foreach ($filtres as $_critere => $_valeur) {
			$where[] = "${_critere}=" . sql_quote($_valeur);
		}
	}

	$depots = sql_allfetsel($select, $from, $where);

	return $depots;
}
