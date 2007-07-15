<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function FormulaireUpload_upgrade($nom_meta_base_version,$version_cible) {
	spip_log("FormulaireUpload_upgrade $nom_meta_base_version => $version_cible");
	include_spip('base/formulaire_upload');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();

	ecrire_meta($nom_meta_base_version, $version_cible, 'non');
	ecrire_metas();
}

function FormulaireUpload_vider_tables($nom_meta_base_version) {
	spip_log("FormulaireUpload_vider_tables");
#	spip_query("DROP TABLE spip_documents_auteurs");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>