<?php

include_spip('base/create');

function svp_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
		
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
		
	if ($current_version=="0.0") {
		include_spip('base/svp_declarer');
		maj_tables(array('spip_depots','spip_plugins','spip_depots_plugins','spip_paquets'));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);

		spip_log('INSTALLATION BDD', 'svp_actions.' . _LOG_INFO);
	}
}

function svp_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_depots");
	sql_drop_table("spip_plugins");
	sql_drop_table("spip_depots_plugins");
	sql_drop_table("spip_paquets");
	effacer_meta($nom_meta_base_version);

	spip_log('DESINSTALLATION BDD', 'svp_actions.' . _LOG_INFO);
}

?>
