<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras_gerer');
include_spip('base/gmaps_v3');
	
function gmaps_v3_upgrade($nom_meta_base_version,$version_cible){
	$champs = gmaps_v3_declarer_champs_extras();
	installer_champs_extras($champs, $nom_meta_base_version, $version_cible);
}

function gmaps_v3_vider_tables($nom_meta_base_version) {
	$champs = gmaps_v3_declarer_champs_extras();
	desinstaller_champs_extras($champs, $nom_meta_base_version);
}
