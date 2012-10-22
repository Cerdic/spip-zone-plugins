<?php
/**
 * Plugin Feuille
 * (c) 2012 chankalan
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function feuille_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(array('maj_tables', array('spip_feuilles')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function feuille_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_feuilles");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('feuille')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('feuille')));
	sql_delete("spip_forum",                 sql_in("objet", array('feuille')));

	effacer_meta($nom_meta_base_version);
}

?>