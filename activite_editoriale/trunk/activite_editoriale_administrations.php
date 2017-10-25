<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');
include_spip('base/activite_editoriale');

function activite_editoriale_upgrade($nom_meta_base_version,$version_cible) {
	$maj = array();
	cextras_api_upgrade(activite_editoriale_declarer_champs_extras(), $maj['create']);
	cextras_api_upgrade(activite_editoriale_declarer_champs_extras(), $maj['1.0.0']);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function activite_editoriale_vider_tables($nom_meta_base_version) {
	cextras_api_vider_tables(activite_editoriale_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}