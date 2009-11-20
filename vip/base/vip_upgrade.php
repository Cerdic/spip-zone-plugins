<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function vip_upgrade($nom_meta_base_version,$version_cible) {
	spip_log("vip_upgrade $nom_meta_base_version => $version_cible");
	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('base/vip');
	creer_base();

	ecrire_meta($nom_meta_base_version, $version_cible, 'non');
	ecrire_metas();
}

function vip_vider_tables($nom_meta_base_version) {
	spip_log("vip_vider_tables");
	spip_query("DROP TABLE spip_vips");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>
