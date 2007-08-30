<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function Indexation_upgrade($nom_meta_base_version,$version_cible) {
	spip_log("Indexation_upgrade $nom_meta_base_version => $version_cible");
	include_spip('base/indexation');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();

	ecrire_meta($nom_meta_base_version, $version_cible, 'non');
	ecrire_metas();
}

function Indexation_vider_tables($nom_meta_base_version) {
	spip_log("Indexation_vider_tables");
	spip_query("DROP TABLE spip_indexation");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>