<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

function sympatic_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	if (	(!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/sympatic');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}

function sympatic_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_sympatic_listes");
	sql_drop_table("spip_sympatic_abonnes");
	effacer_meta($nom_meta_base_version);
}

?>