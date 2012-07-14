<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
function poauth_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	$version_base = 0.1;

    //Changement de version ou premiere installation
    
    spip_log("upgrade","poauth");
    
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			OR (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		spip_log("maj","poauth");
		include_spip('base/poauth');
		//si premiere activation
		if ($current_version==0.0) {
		    spip_log('creation','poauth');
			include_spip('base/create');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_base,'non');
		}
	}
}

function poauth_vider_tables($nom_meta_base_version) {
    sql_drop_table('spip_oauth_server_registry');
    sql_drop_table('spip_oauth_server_token');
    sql_drop_table('spip_oauth_server_nonce');    
	effacer_meta($nom_meta_base_version);
}
	

?>
