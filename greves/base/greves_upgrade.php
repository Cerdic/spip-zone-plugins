<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/meta');
include_spip('base/create');
function greves_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
}
function greves_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_greves");
	effacer_meta($nom_meta_base_version);
}
?>
