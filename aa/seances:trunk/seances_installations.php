<?php
include_spip('inc/meta');
include_spip('base/create');

function seances_upgrade($nom_meta_base_version, $version_cible){
	$current_version = '0.0';
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version == '0.0') {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version = $version_cible);
		sql_alter('TABLE spip_rubriques ADD seance tinyint(1) DEFAULT 0 NOT NULL');
	}
}

function seances_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_seances_endroits',true);
	sql_drop_table('spip_seances',true);
	sql_alter('TABLE spip_rubriques DROP COLUMN seance');
	effacer_meta($nom_meta_base_version);
}
?>