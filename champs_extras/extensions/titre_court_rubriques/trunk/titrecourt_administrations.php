<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');
include_spip('base/titrecourt');
	
function titrecourt_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	
	$table_champs = titrecourt_declarer_champs_extras();
	foreach($table_champs as $table=>$champs)
		$maj['create'][]=array('champs_extras_creer',$table, $champs);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
	
}

function titrecourt_vider_tables($nom_meta_base_version) {
	$table_champs = titrecourt_declarer_champs_extras();
	foreach($table_champs as $table=>$champs)
		champs_extras_supprimer($table, $champs);
	effacer_meta($nom_meta_base_version);
}
?>
