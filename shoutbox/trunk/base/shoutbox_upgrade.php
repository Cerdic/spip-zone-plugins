<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function Shoutbox_upgrade($nom_meta_base_version,$version_cible) {
	spip_log("Shoutbox_upgrade $nom_meta_base_version => $version_cible");
	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('base/shoutbox');
	creer_base();

	ecrire_meta($nom_meta_base_version, $version_cible, 'non');
	ecrire_metas();
}

function Shoutbox_vider_tables($nom_meta_base_version) {
	spip_log("Shoutbox_vider_tables");
	spip_query("DROP TABLE spip_shoutbox");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>