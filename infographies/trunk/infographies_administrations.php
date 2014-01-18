<?php
/**
 * Infographies
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Distribué sous licence GNU/GPL
 *
 * Installation et désinstallation du plugin
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function infographies_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();

	$maj['create'] = array(
		array('maj_tables',array('spip_infographies','spip_infographies_datas','spip_infographies_donnees','spip_infographies_datas_liens'))
	);
	
	$maj['0.1.0'] = array(
		array('maj_tables',array('spip_infographies_datas'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * 
 * Désinstallation du plugin
 * 
 * On supprime :
 * -* Les quatre tables créées
 * -* Les metas de configuration
 * 
 * @param float $nom_meta_base_version
 */
function infographies_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_infographies");
	sql_drop_table("spip_infographies_datas");
	sql_drop_table("spip_infographies_donnees");
	sql_drop_table("spip_infographies_datas_liens");
	effacer_meta('infographies');
	effacer_meta($nom_meta_base_version);
}

?>