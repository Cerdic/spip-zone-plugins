<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des tables mesabonnes
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function mesabonnes_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	
	// Première installation
	$maj['create'] = array(
		array('maj_tables', array('spip_mesabonnes')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}
	
function mesabonnes_vider_tables() {
		sql_drop_table("spip_mesabonnes");
		effacer_meta('mesabonnes_base_version');
}



