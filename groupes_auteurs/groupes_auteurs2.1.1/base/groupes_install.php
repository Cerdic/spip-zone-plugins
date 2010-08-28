<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/meta');

function groupes_upgrade($nom_meta_base_version, $version_cible) {
	$current_version = 0.0;
	spip_log('install groupes nom meta base version : '.$nom_meta_base_version, 'groupes');
	spip_log('install groupes version cible : '.$version_cible, 'groupes');
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
	            || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
	    include_spip('base/groupes_pipelines');
		include_spip('base/create'); 
		include_spip('base/abstract_sql');
	    creer_base(); 	
	    
	    effacer_meta($nom_meta_base_version);
	    ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	    spip_log('install groupes', 'groupes');
	}
}
function groupes_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	include_spip('base/abstract_sql');
	sql_drop_table("spip_groupes");
	sql_drop_table("spip_groupes_auteurs");
	sql_drop_table("spip_groupes_zones");
	spip_log('vider table fin', 'groupes');
}
?>