<?php
/**
 * Plugin Campagnes publicitaires
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function campagnes_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_encarts', 'spip_campagnes', 'spip_annonceurs', 'spip_campagnes_clics', 'spip_campagnes_vues'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function campagnes_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_encarts");
	sql_drop_table("spip_campagnes");
	sql_drop_table("spip_annonceurs");
	sql_drop_table("spip_campagnes_clics");
	sql_drop_table("spip_campagnes_vues");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('encart', 'campagne', 'annonceur')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('encart', 'campagne', 'annonceur')));
	sql_delete("spip_forum",                 sql_in("objet", array('encart', 'campagne', 'annonceur')));

	effacer_meta($nom_meta_base_version);
}

?>
