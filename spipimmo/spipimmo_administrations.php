<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
/**
 * Fonction d'installation, mise a jour de la base
 */
function spipimmo_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	include_spip('base/importer_spip_spipimmo_types_offres');
	$maj['create'] = array(
		array('maj_tables',array('spip_spipimmo_annonces','spip_spipimmo_documents_annonces','spip_spipimmo_negociateurs','spip_spipimmo_proprietaires','spip_spipimmo_types_offres')),
		array('importer_spip_spipimmo_types_offres')
	);
	$maj['0.0.3'] = array(
		array('maj_tables',array('spip_spipimmo_proprietaires'))
	);
	$maj['0.0.4'] = array(
		array('maj_tables',array('spip_spipimmo_proprietaires'))
	);
	$maj['0.0.5'] = array(
		array('maj_tables',array('spip_spipimmo_negociateurs'))
	);
	$maj['0.0.7'] = array(
		array('maj_tables',array('spip_spipimmo_annonces','spip_spipimmo_negociateurs','spip_spipimmo_proprietaires')),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de desinstallation
 */
function spipimmo_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_spipimmo_annonces");
	sql_drop_table("spip_spipimmo_documents_annonces");
	sql_drop_table("spip_spipimmo_proprietaires");
	sql_drop_table("spip_spipimmo_negociateurs");
	sql_drop_table("spip_spipimmo_types_offres");
	effacer_meta($nom_meta_base_version);
}

?>
