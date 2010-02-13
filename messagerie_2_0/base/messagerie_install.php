<?php
/*
 * Plugin messagerie / gestion des messages
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

	include_spip('inc/meta');
	
	/**
	 * Fonction d'upgrade/maj (rien a faire, les tables sont natives Spip)
	 *
	 * @param string $nom_meta_base_version
	 * @param string $version_cible
	 */
	function messagerie_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
				ecrire_meta($nom_meta_base_version,$current_version='1.0.0','non');
		}
	}
	
	/**
	 * Fonction de desinstall (rien a faire, les tables sont natives Spip)
	 *
	 * @param unknown_type $nom_meta_base_version
	 */
	function messagerie_vider_tables($nom_meta_base_version) {
		effacer_meta($nom_meta_base_version);
	}

?>