<?php
     if (!defined("_ECRIRE_INC_VERSION")) return;
     function trad_rub_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/trad_rub_tables');
			// cas d'une installation
		if ($current_version==0.0){
			include_spip('base/create');
			maj_tables('spip_rubriques');
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
			}
		}	
     	}
	function trad_rub_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_formulaires_reponses DROP page");
     effacer_meta($nom_meta_base_version);
     }
?>