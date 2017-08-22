<?php
/**
 * Plugin traduire_texte
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Déclarer la table 'spip_traductions'
 *
 * @param array $tables_auxiliaires
 * @return array
 */
function traduiretexte_declarer_tables_auxiliaires($tables_auxiliaires) {
	$tables_auxiliaires['spip_traductions'] = array(
		"field" => array(
			"hash" => "varchar(32) NOT NULL",
			"langue" => "varchar(5) NOT NULL",
			"texte" => "text NOT NULL"
		),
		"key" => array(
			"KEY hash" => "hash",
			"KEY langue" => "langue",
		)
	);
	return $tables_auxiliaires;
}

/**
 * Installation du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 */
function traduiretexte_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(array('maj_tables', array('spip_traductions')));
	$maj['1.0.0'] = array(array('maj_tables', array('spip_traductions')));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Désinstallation du plugin
 * @param string $nom_meta_base_version
 */
function traduiretexte_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	sql_drop_table("spip_traductions");
}

