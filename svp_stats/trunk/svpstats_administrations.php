<?php

include_spip('base/create');

function svpstats_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
		
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
		
	if ($current_version=="0.0") {
		include_spip('base/svpstats_declarer');
		maj_tables(array('spip_plugins', 'spip_plugins_stats'));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);

		spip_log('MODULE STATS - INSTALLATION BDD', 'svp_actions.' . _LOG_INFO);
	}
}

function svpstats_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_plugins DROP COLUMN nbr_sites");
	sql_alter("TABLE spip_plugins DROP COLUMN popularite");
	sql_drop_table("spip_plugins_stats");
	effacer_meta($nom_meta_base_version);

	spip_log('MODULE STATS - DESINSTALLATION BDD', 'svp_actions.' . _LOG_INFO);
}

?>
