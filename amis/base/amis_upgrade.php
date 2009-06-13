<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


	include_spip('inc/meta');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	
	/**
	 * Mises a jour des tables de gestion des amis lors des montees de version du code
	 *
	 * @param texte $nom_meta_base_version
	 * @param texte $version_cible
	 */
	function amis_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				if (include_spip('base/amis')){
					creer_base();
					echo "Amis Install<br/>";
					ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
				}
				else return;
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
		}
	}
	
	/**
	 * Suppression des tables amis lors de la desinstallation
	 *
	 * @param texte $nom_meta_base_version
	 */
	function amis_vider_tables($nom_meta_base_version) {
		sql_drop_table('spip_amis');
		effacer_meta($nom_meta_base_version);
	}

?>