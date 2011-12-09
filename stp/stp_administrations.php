<?php

include_spip('base/create');
include_spip('base/svp_declarer');


function stp_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'][] = array('maj_tables', 'spip_paquets');

	include_spip('base/upgrade');
	maj_plugin($nom_meta_version_base, $version_cible, $maj);	
}


function stp_vider_tables($nom_meta_base_version) {
	$stp_paquets = stp_declarer_tables_principales(array('stp_paquets' => array()));
	foreach (array_keys($stp_paquets['stp_paquets']['field']) as $champ) {
		sql_alter("TABLE spip_paquets DROP $champ");
	}
	effacer_meta($nom_meta_base_version);
}

?>
