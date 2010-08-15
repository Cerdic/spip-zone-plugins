<?php

spipbb_log('included',2,__FILE__);

include_spip('inc/spipbb_inc_metas');
include_spip('base/create');

function spipbb_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
}

function spipbb_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_spipbb");
	effacer_meta($nom_meta_base_version);
}
?>
