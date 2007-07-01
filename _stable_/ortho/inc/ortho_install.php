<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function Ortho_upgrade($nom_meta_base_version,$version_cible) {
	spip_log("ortho_upgrade $nom_meta_base_version => $version_cible");
	include_spip('base/ortho');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();
	ecrire_meta($nom_meta_base_version, $version_cible, 'non');
	ecrire_metas();
}

function Ortho_vider_tables($nom_meta_base_version) {
	spip_log("ortho_vider_tables");
	spip_query("DROP TABLE spip_ortho_cache");
	spip_query("DROP TABLE spip_ortho_dico");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>