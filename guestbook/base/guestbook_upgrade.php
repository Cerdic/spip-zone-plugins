<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008 - 2010
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	 
include_spip('inc/meta');
include_spip('base/create');

function guestbook_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (isset($GLOBALS['meta'][$nom_meta_base_version])) {
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	}
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version])) || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if ($current_version==0.0){
			creer_base();
			ecrire_meta($nom_meta_base_version, $version_cible,'non');
		}
		if (version_compare($current_version,'2.0','<')){
			maj_tables('spip_guestbook');
			maj_tables('spip_guestbook_reponses');
			ecrire_meta($nom_meta_base_version,$current_version='2.0');
		}
		if (version_compare($current_version,'2.1','<')){
			maj_tables('spip_guestbook');
			maj_tables('spip_guestbook_reponses');
			ecrire_meta($nom_meta_base_version,$current_version='2.1');
		}
		ecrire_metas();
	}
}
function guestbook_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_guestbook");
	sql_drop_table("spip_guestbook_reponses");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}
?>