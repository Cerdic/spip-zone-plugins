<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Date de création
 *
 * @plugin     Date de création
 * @copyright  2018
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Datecreation\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/meta');
include_spip('inc/config');

/**
 * Declaration des tables principales
 * Déclare le champ date_creation sur les tables d'objets qui en disposent
 *
 * @param array $tables_principales
 *
 * @return array
 */
function date_creation_declarer_tables_principales($tables_principales) {
	$tables = unserialize(lire_config('date_creation/objets'));
	if (is_array($tables)) {
		foreach ($tables as $table) {
			$tables_principales[$table]['field']['date_creation'] = 'datetime DEFAULT "0000-00-00 00:00:00" NOT NULL';
		}
	}

	return $tables_principales;
}

/**
 * Fonction d'installation et de mise à jour du plugin Date de création.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 *
 * @return void
 **/
function date_creation_upgrade($nom_meta_base_version, $version_cible) {
	date_creation_creer_champs_date_creation();

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, array());
}

/**
 * Créer un champ date_creation dans les tables d'objets qui n'en ont pas encore un.
 *
 * @return array Liste des tables ayant un champ date_creation
 */
function date_creation_creer_champs_date_creation() {
	include_spip('base/abstract_sql');
	$tables        = array();
	$tables_objets = lister_tables_objets_sql();
	foreach ($tables_objets as $table => $desc) {
		$champs_table = sql_showtable($table);
		if (!isset($champs_table['field']['date_creation'])) {
			$sql = 'TABLE ' . $table . ' ADD date_creation datetime DEFAULT "0000-00-00 00:00:00" NOT NULL';
			sql_alter($sql);
			spip_log('ALTER ' . $sql, 'date_creation');
			$tables[] = $table;
		} else if (strpos($champs_table['field']['date_creation'], 'datetime') !== false) {
			$tables[] = $table;
		}
	}
	ecrire_config('date_creation/objets', serialize($tables));

	return $tables;
}

/**
 * Fonction de désinstallation du plugin Date de création.
 * Supprime les champs date_creation des tables d'objets.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 *
 * @return void
 **/
function date_creation_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	$tables = unserialize(lire_config('date_creation/objets'));
	if (is_array($tables)) {
		foreach ($tables as $table) {
			$sql = 'TABLE ' . $table . ' DROP date_creation';
			sql_alter($sql);
			spip_log('ALTER ' . $sql, 'date_creation');
		}
	}
	effacer_meta($nom_meta_base_version);
	effacer_meta('date_creation');
}
