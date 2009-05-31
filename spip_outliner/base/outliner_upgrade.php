<?php
/*
 * Spip-Outliner
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

	include_spip('inc/meta');
	
	function outliner_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			
			if ($current_version==0.0){
				include_spip('base/forms_base_api');
				$f = find_in_path('base/Outliner.xml');
				Forms_creer_table($f,'outline');
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}

			ecrire_metas();
		}
	}
	
	function outliner_vider_tables($nom_meta_base_version) {
		include_spip('base/forms_base_api');
		Forms_supprimer_tables('outline');
		
		// fin
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>