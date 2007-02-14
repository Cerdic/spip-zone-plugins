<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

	include_spip('inc/meta');
	
	function smslist_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			
			if ($current_version==0.0){
				$f = find_in_path('base/Abonnes_SMS.xml');
				include_spip('base/forms_base_api');
				Forms_creer_table($f,'smslist_liste');
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
	
			ecrire_metas();
		}
	}
	
	function smslist_vider_tables($nom_meta_base_version) {
		include_spip('base/forms_base_api');
		Forms_supprimer_tables('smslist_liste');
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>