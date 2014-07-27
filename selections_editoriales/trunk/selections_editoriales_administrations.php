<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Sélections éditoriales
 *
 * @plugin     Sélections éditoriales
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Selections_editoriales\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Sélections éditoriales.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function selections_editoriales_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_selections', 'spip_selections_liens', 'spip_selections_contenus')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Sélections éditoriales.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function selections_editoriales_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_selections");
	sql_drop_table("spip_selections_liens");
	sql_drop_table("spip_selections_contenus");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('selection', 'selections_contenu')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('selection', 'selections_contenu')));
	sql_delete("spip_forum",                 sql_in("objet", array('selection', 'selections_contenu')));

	effacer_meta($nom_meta_base_version);
}

?>