<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation/maj des tables breves
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function spipservice_upgrade($nom_meta_base_version, $version_cible){
	spip_log("- Creation de la table 'spip_spipservice'", "spipservice");
	$maj = array();
	$maj['create'] = array(
			array('maj_tables', array('spip_spipservice')),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables breves
 *
 * @param string $nom_meta_base_version
 */
function spipservice_vider_tables($nom_meta_base_version) {
	spip_log("- Suppression de la table 'spip_spipservice'", "spipservice");
	sql_drop_table("spip_spipservice");
	effacer_meta($nom_meta_base_version);
}

?>
