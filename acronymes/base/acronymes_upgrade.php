<?php

	include_spip('inc/meta');
	function acronymes_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				if (include_spip('base/forms_base_api')){
					$f = find_in_path('base/Sigles.xml');
					Forms_creer_table($f,'acronymes_sigles');
					echo "Acronym Install<br/>";
					ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
				}
				else return;
			}
			ecrire_metas();
		}
	}
	
	function acronymes_vider_tables($nom_meta_base_version) {
		include_spip('base/forms_base_api');
		Forms_supprimer_tables('acronymes_sigles');
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>
