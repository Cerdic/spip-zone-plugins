<?php

function refresher_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('refresher_cron','refresher_urls'))
	);
 	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function refresher_vider_tables($nom_meta_base_version) {
	sql_drop_table("refresher_cron");
	sql_drop_table("refresher_urls");
	effacer_meta($nom_meta_base_version);
}

?>