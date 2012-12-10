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
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function spipimmo_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_spipimmo_annonces','spip_spipimmo_documents_annonces','spip_spipimmo_types_offres')),
		array('peupler_base_pays')
	);

	include_spip('base/upgrade');
	include_spip('base/pays_peupler_base');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function spipimmo_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_spipimmo_annonces");
	sql_drop_table("spip_spipimmo_documents_annonces");
	sql_drop_table("spip_spipimmo_types_offres");
	effacer_meta($nom_meta_base_version);
}

?>
