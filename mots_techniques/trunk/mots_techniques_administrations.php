<?php

include_spip('inc/cextras');
include_spip('base/mots_techniques');

function mots_techniques_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$table_champs = mots_techniques_declarer_champs_extras();
	foreach($table_champs as $table=>$champs)
		$maj['create'][] = array('champs_extras_creer',$table, $champs);
		
	$maj['0.2'][] = array('sql_alter', "TABLE spip_groupes_mots DROP affiche_formulaire");
		
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function mots_techniques_vider_tables($nom_meta_base_version) {
	$table_champs = mots_techniques_declarer_champs_extras();
	foreach($table_champs as $table=>$champs)
		champs_extras_supprimer($table, $champs);
	effacer_meta($nom_meta_base_version);
}

?>
