<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras_gerer');
include_spip('base/type_articles');
	
function type_articles_upgrade($nom_meta_base_version,$version_cible){
	$champs = type_articles_declarer_champs_extras();
	installer_champs_extras($champs, $nom_meta_base_version, $version_cible);
}

function type_articles_vider_tables($nom_meta_base_version) {
	$champs = type_articles_declarer_champs_extras();
	desinstaller_champs_extras($champs, $nom_meta_base_version);
}
