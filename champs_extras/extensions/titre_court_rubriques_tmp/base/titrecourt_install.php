<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras_gerer');
include_spip('base/titrecourt');
	
function titrecourt_upgrade($nom_meta_base_version,$version_cible){
	$champs = titrecourt_declarer_champs_extras();
	installer_champs_extras($champs, $nom_meta_base_version, $version_cible);
}

function titrecourt_vider_tables($nom_meta_base_version) {
	$champs = titrecourt_declarer_champs_extras();
	desinstaller_champs_extras($champs, $nom_meta_base_version);
}
?>
