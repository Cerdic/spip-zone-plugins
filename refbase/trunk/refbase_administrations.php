<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function refbase_upgrade($nom_meta_base_version,$version_cible){
	ecrire_meta($nom_meta_base_version,$version_cible);
}

function refbase_vider_tables($nom_meta_base_version) {
	effacer_meta("refbase");
	effacer_meta($nom_meta_base_version);
}

?>