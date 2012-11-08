<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr) V0.1
* @author: Pierre KUHN V1
*
* Copyright (c) 2011-12
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
function anaclic_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_zones','spip_zones_liens')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function anaclic_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_clics");
	effacer_meta($nom_meta_base_version);
}

?>
