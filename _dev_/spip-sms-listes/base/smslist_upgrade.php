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
				include_spip('base/forms_base_api');
				$f = find_in_path('base/Abonnes_SMS.xml');
				Forms_creer_table($f,'smslist_abonne');
				$f = find_in_path('base/Liste_de_diffusion_SMS.xml');
				Forms_creer_table($f,'smslist_liste');
				$f = find_in_path('base/SMS.xml');
				Forms_creer_table($f,'smslist_message');
				$f = find_in_path('base/Boite_d_envoi_des_SMS.xml');
				Forms_creer_table($f,'smslist_boiteenvoi');
				$f = find_in_path('base/Comptes_SMS.xml');
				Forms_creer_table($f,'smslist_compte');
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
	
			ecrire_metas();
		}
	}
	
	function smslist_vider_tables($nom_meta_base_version) {
		include_spip('base/forms_base_api');
		/*Forms_supprimer_tables('smslist_liste');
		Forms_supprimer_tables('smslist_abonne');
		Forms_supprimer_tables('smslist_message');
		Forms_supprimer_tables('smslist_boiteenvoi');
		Forms_supprimer_tables('smslist_compte');*/
		
		// fin
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>