<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function pb_couleur_rubrique_upgrade($nom_meta_base_version, $version_cible){
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function pb_couleur_rubrique_vider_tables($nom_meta_base_version) {
	effacer_config("pb_couleur_rubrique/afficher"); // je le laisse mais il semble inoperant
	effacer_meta($nom_meta_base_version);
}

?>