<?php

include_spip('inc/meta');

include_spip('base/create');

function catalogue_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
}

function catalogue_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_variantes");
	sql_drop_table("spip_produits");
	sql_drop_table("spip_familles");
	effacer_meta($nom_meta_base_version);
}

?>