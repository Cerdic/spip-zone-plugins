<?php

include_spip('inc/cextras');
include_spip('base/mots_techniques');

function mots_techniques_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();

	cextras_api_upgrade(mots_techniques_declarer_champs_extras(), $maj['create']);
		
	$maj['0.2'][] = array('sql_alter', "TABLE spip_groupes_mots DROP affiche_formulaire");
		
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function mots_techniques_vider_tables($nom_meta_base_version) {
	cextras_api_vider_tables(mots_techniques_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}

?>
