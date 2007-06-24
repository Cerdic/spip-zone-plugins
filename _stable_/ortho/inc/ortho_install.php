<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function Ortho_upgrade($nom_meta_base_version,$version_cible) {
	include_spip('base/ortho');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();
}

function Ortho_vider_tables($nom_meta_base_version) {
	spip_query("DROP TABLE spip_ortho_cache");
	spip_query("DROP TABLE spip_ortho_dico");
}

?>