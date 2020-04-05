<?php
	/**
	 * savecfg
	 *
	 * Copyright (c) 2009
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function savecfg_install($action){
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['savecfg_base_version']) AND ($GLOBALS['meta']['savecfg_base_version']>=$version_base));
			break;
		case 'install':
			savecfg_upgrade('savecfg_base_version',0.2);
			break;
		case 'uninstall':
			savecfg_vider_tables('savecfg_base_version');
			break;
	}
}
function savecfg_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version])) || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('base/savecfg_install');
			creer_base();
			ecrire_meta($nom_meta_base_version,$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			sql_alter("TABLE `spip_savecfg` DROP `version`");
		}
		ecrire_metas();
	}
}
function savecfg_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_savecfg");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}
?>