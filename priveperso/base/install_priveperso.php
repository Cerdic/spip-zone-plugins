<?php
    
include_spip('inc/meta');
include_spip('base/create');

function priveperso_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";

	if (isset($GLOBALS['meta'][$nom_meta_base_version])) {
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	}

	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	if (version_compare($current_version,"0.4","<")){
		maj_tables('spip_priveperso');
		maj_tables('spip_priveperso_texte');
		ecrire_meta($nom_meta_base_version,$current_version="0.4");
	}

}
	
function priveperso_vider_tables($nom_meta_base_version) {

		sql_drop_table('spip_priveperso');
		sql_drop_table('spip_priveperso_texte');
		effacer_meta($nom_meta_base_version);
		effacer_meta('priveperso_version');
		ecrire_metas();	
	
}
	

?>