<?php
/**
* Plugin SPIPmotion
* par kent1 (http://kent1.sklunk.net)
*
* Copyright (c) 2007-2009
* Logiciel libre distribué sous licence GNU/GPL.
*
* Installation / Désinstallation des tables
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function spipmotion_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/spipmotion');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			maj_tables('spip_documents');
			echo '<p>'._T('spipmotion:install_creation_base').'</p>';
			echo '<p>'._T('spipmotion:install_ajout_champs_documents').'</p>';
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			sql_alter("TABLE spip_spipmotion_attentes ADD `id_auteur` BIGINT(21) NOT NULL DEFAULT '0' AFTER `id_article`");
			sql_alter("TABLE spip_spipmotion_attentes ADD INDEX ( `id_auteur` )");
			ecrire_meta($nom_meta_base_version,$current_version=0.2);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.2)).'</p>';
		}
		if (version_compare($current_version,'0.3','<')){
			sql_alter("TABLE spip_documents ADD `duree` VARCHAR(255) DEFAULT '' NOT NULL AFTER `hauteur`");
			sql_alter("TABLE spip_documents ADD `framecount` INTEGER AFTER `duree`");
			sql_alter("TABLE spip_documents ADD `framerate` INTEGER AFTER `framecount`");
			sql_alter("TABLE spip_documents ADD `pixelformat` VARCHAR(255) DEFAULT '' NOT NULL AFTER `framerate`");
			sql_alter("TABLE spip_documents ADD `bitrate` INTEGER AFTER `pixelformat`");
			sql_alter("TABLE spip_documents ADD `videobitrate` INTEGER AFTER `bitrate`");
			sql_alter("TABLE spip_documents ADD `audiobitrate` INTEGER AFTER `videobitrate`");
			sql_alter("TABLE spip_documents ADD `audiosamplerate` INTEGER AFTER `audiobitrate`");
			sql_alter("TABLE spip_documents ADD `videocodec` VARCHAR(255) DEFAULT '' NOT NULL AFTER `audiosamplerate`");
			sql_alter("TABLE spip_documents ADD `audiocodec` VARCHAR(255) DEFAULT '' NOT NULL AFTER `videocodec`");
			sql_alter("TABLE spip_documents ADD `audiochannels` INTEGER AFTER `audiocodec`");
			ecrire_meta($nom_meta_base_version,$current_version=0.3);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.3)).'</p>';
		}
		if (version_compare($current_version,'0.4','<')){
			sql_alter("TABLE spip_spipmotion_attentes CHANGE `id_article` `id_objet` BIGINT(21) NOT NULL DEFAULT '0'");
			sql_alter("TABLE spip_spipmotion_attentes ADD `objet` VARCHAR(25) AFTER `id_objet`");
			ecrire_meta($nom_meta_base_version,$current_version=0.4);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.4)).'</p>';
		}
		if (version_compare($current_version,'0.5','<')){
			sql_alter("TABLE spip_documents ADD `id_orig` BIGINT(21) NOT NULL AFTER `audiochannels`");
			ecrire_meta($nom_meta_base_version,$current_version=0.5);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.5)).'</p>';
		}
		if (version_compare($current_version,'0.6','<')){
			sql_alter("TABLE spip_spipmotion_attentes ADD `extension` VARCHAR(10) DEFAULT '' NOT NULL AFTER `id_auteur`");
			ecrire_meta($nom_meta_base_version,$current_version=0.6);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.6)).'</p>';
		}
		/**
		 * TODO : générer un htaccess dans le répertoire script_bash/
		 * TODO : insérer une préconfiguration par défaut
		 */
	}
}

function spipmotion_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table("spip_spipmotion_attentes");
	effacer_meta($nom_meta_base_version);
}
?>