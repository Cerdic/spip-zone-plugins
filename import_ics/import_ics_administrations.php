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
		array('maj_tables', array('spip_almanachs', 'spip_almanachs_liens')),
		array('maj_tables',array('spip_evenements')),
		array('sql_alter',"TABLE spip_evenements ADD uid text NOT NULL"),
		array('sql_alter',"TABLE spip_evenements ADD sequence text NOT NULL"),
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

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('almanach')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('almanach')));
	sql_delete("spip_forum",                 sql_in("objet", array('almanach')));

	effacer_meta($nom_meta_base_version);
}

?>