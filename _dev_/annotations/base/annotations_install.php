<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function Annotations_upgrade($nom_meta_base_version,$version_cible) {
	spip_log("Annotations_upgrade $nom_meta_base_version => $version_cible");
	include_spip('base/annotations');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();

	ecrire_meta($nom_meta_base_version, $version_cible, 'non');
	ecrire_metas();
}

function Annotations_vider_tables($nom_meta_base_version) {
	spip_log("Annotations_vider_tables");
	spip_query("DROP TABLE spip_annotations");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>
