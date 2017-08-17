<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');
include_spip('base/multidomaines');
	
function multidomaines_upgrade($nom_meta_base_version,$version_cible){

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
	
}

function multidomaines_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
?>
