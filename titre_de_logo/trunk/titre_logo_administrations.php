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
	$maj['create'] = array();
	foreach ($tables_objets as $table) {
		$maj['create'][] = array('sql_alter',"TABLE $table ADD titre_logo text DEFAULT '' NOT NULL");
		$maj['create'][] = array('sql_alter',"TABLE $table ADD descriptif_logo text DEFAULT '' NOT NULL");
	}

	$maj['3.0.3'] = array(
		array('titre_logo_nettoyer', array())
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
	$tables_objets = titre_logo_liste_tables();
	$trouver_table = charger_fonction('trouver_table', 'base');
	foreach ($tables_objets as $table) {
		$desc = $trouver_table($table);
		if (!isset($desc['field']['titre_logo'])) {
			sql_alter("TABLE $table ADD titre_logo text DEFAULT '' NOT NULL");
		}
		if (!isset($desc['field']['descriptif_logo'])) {
			sql_alter("TABLE $table ADD descriptif_logo text DEFAULT '' NOT NULL");
		}
	}
}

/**
 * supprimer les champs 'titre_logo' et 'descriptif_logo' dans les tables de la black_list
 */
function titre_logo_nettoyer() {
	$black_liste = titre_logo_black_list();
	foreach ($black_liste as $table) {
		sql_alter("TABLE $table DROP titre_logo");
		sql_alter("TABLE $table DROP descriptif_logo");
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
	$tables_objets = titre_logo_liste_tables();
	foreach ($tables_objets as $table) {
		sql_alter("TABLE $table DROP titre_logo");
		sql_alter("TABLE $table DROP descriptif_logo");
	}

	effacer_meta('titre_logo');
	effacer_meta($nom_meta_base_version);
}

/**
 * Fournir la liste des tables pour lesquels fournir les champs 'titre_logo' et 'descriptif_logo'
 * écarter les tables connues pour lesquelles c'est inutile
 * @return array
 */

function titre_logo_liste_tables() {
	$tables_objets	 = array_keys(lister_tables_objets_sql());
	$black_liste	   = titre_logo_black_list();
	$list_tables_logos = array_diff($tables_objets, $black_liste);

	return $list_tables_logos;
}

/**
 * Black list : les tables connues pour lesquelles il est inutile de fournir les champs 'titre_logo' et 'descriptif_logo'
 * @return array
 */

function titre_logo_black_list() {
	$black_list = array(0 => 'spip_depots',
						1 => 'spip_documents',
						2 => 'spip_forum',
						3 => 'spip_messages',
						4 => 'spip_paquets',
						5 => 'spip_petitions',
						6 => 'spip_plugins',
						7 => 'spip_signatures',
						8 => 'spip_syndic_articles');
	return $black_list;
}
