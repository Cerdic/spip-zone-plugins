<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function so_upgrade($nom_meta_base_version,$version_cible){
	if ($version_cible > $GLOBALS['meta'][$nom_meta_base_version]){
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == ""){
			echo 'Selection '.$version_cible;
			include_spip('base/create');
			creer_base();
			
		}
		
	}
	//echo fin_boite_info(true);
	ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	ecrire_metas();		
		
}


?>