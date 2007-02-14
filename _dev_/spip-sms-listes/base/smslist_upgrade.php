<?php

	include_spip('inc/meta');
	
	function smslist_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			
			if ($current_version==0.0){
				$f = find_in_path('Abonnes_SMS.xml');
				include_spip('base/forms_base_api');
				Forms_creer_table($f,'smslist_abonnes');
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
	
			ecrire_metas();
		}
	}
	
	function smslist_vider_tables($nom_meta_base_version) {
		include_spip('base/forms_base_api');
		Forms_supprimer_tables('smslist_abonnes');
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>