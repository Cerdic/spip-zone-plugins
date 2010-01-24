<?php
/**
* Plugin GetID3
* par BoOz, kent1
*
* Copyright (c) 2007-2010
* Logiciel libre distribué sous licence GNU/GPL.
*
* Définition des tables
*
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function getid3_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			echo '<p>'._T('getid3:install_ajout_champs_documents').'</p>';
		}
		if (version_compare($current_version,'0.1','<')){
			sql_alter("TABLE spip_documents ADD `duree` VARCHAR(255) DEFAULT '' NOT NULL AFTER `hauteur`");
			sql_alter("TABLE spip_documents ADD `bitrate` INTEGER AFTER `duree`");
			sql_alter("TABLE spip_documents ADD `bitrate_mode` text AFTER `bitrate`");
			sql_alter("TABLE spip_documents ADD `audiosamplerate` INTEGER AFTER `bitrate_mode`");
			sql_alter("TABLE spip_documents ADD `encodeur` text AFTER `audiosamplerate`");
			ecrire_meta($nom_meta_base_version,$current_version=0.1);
			echo _T('getid3:install_mise_a_jour_base',array('version'=>'0.1'));
		}
		if (version_compare($current_version,'0.2','<')){
			sql_alter("TABLE spip_documents ADD `bits` INTEGER AFTER `encodeur`");
			sql_alter("TABLE spip_documents ADD `canaux` text AFTER `bits`");
			ecrire_meta($nom_meta_base_version,$current_version=0.2);
			echo _T('getid3:install_mise_a_jour_base',array('version'=>'0.2'));
		}
	}
}
?>