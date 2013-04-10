<?php
/**
* Plugin Podcast
* par kent1
*
* Copyright (c) 2010
* Logiciel libre distribué sous licence GNU/GPL.
*
* Installation
*
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function podcast_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}

function podcast_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
?>