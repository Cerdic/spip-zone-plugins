<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

function doc2article_upgrade($nom_meta_base_version,$version_cible){
	$current_version = '0.0';
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/doc2article');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}

function doc2article_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_doc2article');
	effacer_meta($nom_meta_base_version);
}

?>