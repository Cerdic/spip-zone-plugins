<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Requêteur SQL
 *
 * @plugin     Requêteur SQL
 * @copyright  2014
 * @author     David Dorchies
 * @licence    GNU/GPL
 * @package    SPIP\Requeteursql\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Requêteur SQL.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function requeteursql_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_sql_requetes')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Requêteur SQL.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function requeteursql_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_sql_requetes");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('sql_requete')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('sql_requete')));
	sql_delete("spip_forum",                 sql_in("objet", array('sql_requete')));

	effacer_meta($nom_meta_base_version);
}

?>