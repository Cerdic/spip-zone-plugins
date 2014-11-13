<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables gis
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function malettre_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	
	// Première installation
	$maj['create'] = array(
		array('maj_tables', array('spip_meslettres')),
    array('malettre_creer_repertoire_documents')
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}
	
function malettre_vider_tables() {
		sql_drop_table("spip_meslettres");
		effacer_meta('malettre_base_version');
}



function malettre_creer_repertoire_documents() {
	   include_spip('inc/getdocument');
	   creer_repertoire_documents('lettre');  
}

?>