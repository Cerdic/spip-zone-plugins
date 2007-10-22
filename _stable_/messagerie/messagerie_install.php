<?php
	include_spip('inc/meta');
	
	function messagerie_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
				ecrire_meta($nom_meta_base_version,$current_version='0.1.0.0','non');
			ecrire_metas();
		}
	}
	
	function messagerie_vider_tables($nom_meta_base_version) {
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>
