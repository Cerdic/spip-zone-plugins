<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin DayFill
 *
 * @plugin     DayFill
 * @copyright  2013
 * @author     Cyril Marion
 * @licence    GNU/GPL
 * @package    SPIP\Dayfill\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin DayFill.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function dayfill_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_projets_activites')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin DayFill.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function dayfill_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_projets_activites");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('projets_activite')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('projets_activite')));
	sql_delete("spip_forum",                 sql_in("objet", array('projets_activite')));

	effacer_meta($nom_meta_base_version);
}

?>