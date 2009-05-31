<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/meta');

function annonces_upgrade($nom_meta, $version_cible) {
	$current_version = 0.0;
	
	if (   (!isset($GLOBALS['meta'][$nom_meta]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta]) != $version_cible)){
		if ($current_version == 0.0){
			ecrire_meta($nom_meta, $current_version = 0.1,'non');
			ecrire_metas();
		}
	}
}

function annonces_vider_tables($nom_meta) {
	include_spip('base/abstract_sql');
	spip_query("UPDATE spip_articles SET "._TYPE."='article' WHERE "._TYPE."='annonces'");
	effacer_meta($nom_meta);
	ecrire_metas();
}

?>