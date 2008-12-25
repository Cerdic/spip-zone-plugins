<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/champsextras');
include_spip('base/postscriptum');
	
function postscriptum_upgrade($nom_meta_base_version,$version_cible){
	$champs = postscriptum_declarer_champs_extras();
	creer_champs_extras($champs, $nom_meta_base_version, $version_cible);
}

function postscriptum_vider_tables($nom_meta_base_version) {
	$champs = postscriptum_declarer_champs_extras();
	vider_champs_extras($champs, $nom_meta_base_version);
}
?>
