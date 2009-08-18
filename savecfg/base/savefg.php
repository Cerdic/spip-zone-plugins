<?php
	/**
	 * SaveFG
	 *
	 * Copyright (c) 2009
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
function savefg_install($action){
	switch ($action){
		case 'test':
			savefg_upgrade('savefg_base_version',0.1);
		break;
		case 'install':
			savefg_upgrade('savefg_base_version',0.1);
		break;
		case 'uninstall':
			savefg_vider_tables('savefg_base_version');
		break;
	}
}
function savefg_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version])) || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('base/savefg_install');
			creer_base();
			ecrire_meta($nom_meta_base_version,$version_cible,'non');
		}
		ecrire_metas();
	}
}
function savefg_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_savefg");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}
?>