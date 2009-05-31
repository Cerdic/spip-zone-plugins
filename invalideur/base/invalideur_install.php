<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function Invalideur_upgrade($nom_meta_base_version,$version_cible) {
	spip_log("Invalideur_upgrade $nom_meta_base_version => $version_cible");
	include_spip('base/invalideur');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();

	ecrire_meta($nom_meta_base_version, $version_cible, 'non');
	ecrire_metas();
}

function Invalideur_vider_tables($nom_meta_base_version) {
	spip_log("Invalideur_vider_tables");
	spip_query("DROP TABLE spip_caches");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>