<?php

	include_spip('inc/meta');
	
	function w3cgh_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
	
			ecrire_metas();
		}
	}
	
	function w3cgh_vider_tables($nom_meta_base_version) {
		effacer_meta('w3cgh_validateurs_actifs');
		foreach($GLOBALS['meta'] as $k=>$v)
			if (strncmp($k,"w3cgh_",6)==0)
				effacer_meta($k);
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>