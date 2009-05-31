<?php
/*
 * Expresso
 * Gestion d'un cache statique Apache
 *
 * Auteurs :
 * Cedric Morin, Yterium.com
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

	include_spip('inc/meta');
	function expresso_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				if ($d = sous_repertoire(_DIR_VAR,"apache"))
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			ecrire_metas();
		}
	}
	
	function expresso_vider_tables($nom_meta_base_version) {
		effacer_meta('expresso');
		$cache = preg_files(_DIR_VAR."apache/");
		foreach($cache as $f)
			@unlink($f);
		@unlink(_DIR_VAR."apache");
		lire_fichier('.htaccess',$htaccess);
		$htaccess = preg_replace(",###EXPRESSO###.*###/EXPRESSO###,ms","###EXPRESSO###",$htaccess);
		ecrire_fichier('.htaccess',$htaccess);
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>
