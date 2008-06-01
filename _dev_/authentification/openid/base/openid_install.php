<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
function openid_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	$version_base = 0.1;

	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/openid');
		if ($current_version==0.0){
			include_spip('base/create');
			maj_tables('spip_auteurs');
			ecrire_meta($nom_meta_base_version,$current_version=$version_base,'non');
		}
	}
}

function openid_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_auteurs DROP openid");
	effacer_meta($nom_meta_base_version);
}
	

?>
