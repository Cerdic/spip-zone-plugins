<?php
/**
 * Plugin Partenaires
 * (c) 2013 Teddy Payet
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function partenaires_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_partenaires', 'spip_partenaires_types', 'spip_partenaires_types_liens')));
        include_spip('base/importer_spip_partenaires_types');
        $maj['create'][] = array('importer_spip_partenaires_types');

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function partenaires_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_partenaires");
	sql_drop_table("spip_partenaires_types");
	sql_drop_table("spip_partenaires_types_liens");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('partenaire', 'partenaires_type')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('partenaire', 'partenaires_type')));
	sql_delete("spip_forum",                 sql_in("objet", array('partenaire', 'partenaires_type')));

	effacer_meta($nom_meta_base_version);
}

?>