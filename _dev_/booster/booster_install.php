<?php
/*
 * Booster
 * Gestion d'un cache statique Apache
 *
 * Auteurs :
 * Cedric Morin, Yterium.com
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

	include_spip('inc/meta');
	function booster_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				if ($d = sous_repertoire(_DIR_CACHE,"apache"))
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			ecrire_metas();
		}
	}
	
	function booster_vider_tables($nom_meta_base_version) {
		effacer_meta('booster');
		$cache = preg_files(_DIR_CACHE."apache/");
		foreach($cache as $f)
			@unlink($f);
		@unlink(_DIR_CACHE."apache");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>
