<?php

/*
 * Plugin Titre de logo
 *
 * @plugin	 Titre de logo
 *
 * @copyright  2015
 * @author	 Arno*
 * @licence	GPL 3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Upgrade des tables.
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function titre_logo_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('base/objets');
	$tables_objets = titre_logo_liste_tables();
	$maj = array();
	$maj['create'] = array(
		array('titre_logo_check_upgrade'),
	);

	$maj['3.1.0'] = array(
		array('titre_logo_check_upgrade')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Une fonction pour verifier que les champs sont bien sur tous les objets,
 * appelee lors de la configuration
 * (cas d'un nouvel objet ajouté apres l'install du plugin).
 */
function titre_logo_check_upgrade() {
	include_spip('base/objets');
	include_spip('base/titre_logo');

	$tables_avec_titre_logo = titre_logo_liste_tables();
	$tables_objets = array_keys(lister_tables_objets_sql());
	$trouver_table = charger_fonction('trouver_table', 'base');
	$trouver_table(''); // vider le cache des descriptions SQL

	foreach ($tables_objets as $table) {
		if (in_array($table, $tables_avec_titre_logo)) {
			titre_logo_upgrader_table($table);
		}
		else {
			titre_logo_nettoyer_table($table);
		}
	}
}

/**`
 * Creer les champs nécessaires à titre logo pour une table sql
 * @param $table
 */
function titre_logo_upgrader_table($table) {
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table = table_objet_sql($table);
	$desc = $trouver_table($table);

	$defs = array(
		'titre_logo' => 'text DEFAULT \'\' NOT NULL',
		'descriptif_logo' => 'text DEFAULT \'\' NOT NULL',
	);

	foreach ($defs as $champ => $defsql) {
		if (!isset($desc['field'][$champ])) {
			sql_alter($q="TABLE $table ADD $champ $defsql");
			spip_log($q, 'titre_logo' . _LOG_DEBUG);
		}
	}
}

/**
 * supprimer les champs 'titre_logo' et 'descriptif_logo' d'une table si pas utilise (tous les champs sont vides)
 * si force=true, on supprime les champs dans tous les cas
 * @param string $table
 * @param bool $force
 */
function titre_logo_nettoyer_table($table, $force = false) {
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table = table_objet_sql($table);
	$desc = $trouver_table($table);
	foreach (array('titre_logo', 'descriptif_logo') as $champ) {
		if (isset($desc['field']['titre_logo'])) {
			$used = false;
			if (!$force) {
				$used = sql_countsel($table, "$champ!=''");
			}
			if ($force or !$used) {
				sql_alter($q = "TABLE $table DROP $champ");
				spip_log($q, 'titre_logo' . _LOG_DEBUG);
			}
			else {
				spip_log("Table $table on conserve le champ $champ car il y a du contenu", 'titre_logo' . _LOG_DEBUG);
			}
		}
	}
}

/**
 * Desinstallation.
 *
 * @param string $nom_meta_base_version
 */
function titre_logo_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	include_spip('base/objets');

	$tables_objets = array_keys(lister_tables_objets_sql());
	foreach ($tables_objets as $table) {
		titre_logo_nettoyer_table($table, true);
	}

	effacer_meta('titre_logo');
	effacer_meta($nom_meta_base_version);
}
