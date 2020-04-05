<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

function sympatic_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/sympatic');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		if ($current_version==0.0){
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.3.0','<')){
			maj_tables('spip_sympatic_listes');
			ecrire_meta($nom_meta_base_version,$current_version="0.3.0",'non');
		}
	}
}

function sympatic_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_sympatic_listes");
	sql_drop_table("spip_sympatic_abonnes");
	effacer_meta($nom_meta_base_version);
}

?>