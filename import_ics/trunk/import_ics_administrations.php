<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Import_ics
 *
 * @plugin     Import_ics
 * @copyright  2013
 * @author     Amaury
 * @licence    GNU/GPL
 * @package    SPIP\Import_ics\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/cextras');
include_spip('base/import_ics');

/**
 * Fonction d'installation et de mise à jour du plugin Import_ics.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function import_ics_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables',array('spip_almanachs', 'spip_almanachs_liens')),
		array('maj_tables',array('spip_evenements')),
		array('sql_alter',"TABLE spip_evenements ADD uid text NOT NULL"),
		array('sql_alter',"TABLE spip_evenements ADD sequence bigint(21) DEFAULT '0' NOT NULL"),
		array('sql_alter',"TABLE spip_evenements ADD last_modified_distant text NOT NULL"),
	);

	$maj['1.0.1'] = array(
		array('maj_tables', array('spip_almanachs')),
	);

	$maj['1.0.2'] = array(
		array('maj_tables', array('spip_almanachs')),
	);
	$maj["1.0.3"] = array(
		cextras_api_upgrade(import_ics_declarer_champs_extras(), $maj['1.0.3']),
	);
	$maj["1.0.4"] = array(
		array('sql_alter',"TABLE spip_evenements ADD last_modified_distant text NOT NULL"),
	);
	$maj["1.0.5"] = array(
		array('sql_alter',"TABLE spip_almanachs ADD derniere_synchro datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Import_ics.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function import_ics_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_almanachs");
	sql_drop_table("spip_almanachs_liens");

	sql_alter("TABLE spip_evenements DROP COLUMN uid");
	sql_alter("TABLE spip_evenements DROP COLUMN sequence");
	sql_alter("TABLE spip_evenements DROP COLUMN last_modified_distant");
  cextras_api_vider_tables(import_ics_declarer_champs_extras());
	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('almanach')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('almanach')));
	sql_delete("spip_forum",                 sql_in("objet", array('almanach')));

	effacer_meta($nom_meta_base_version);
}

?>