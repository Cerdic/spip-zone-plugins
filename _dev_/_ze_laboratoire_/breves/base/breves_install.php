<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function breves_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/breves');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			maj_tables('spip_forum'); 
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
	}
}

function breves_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table("spip_breves");
	sql_drop_table("spip_mots_breves");
	sql_alter("TABLE spip_forum DROP COLUMN id_breve");
	effacer_meta($nom_meta_base_version);
}
?>
