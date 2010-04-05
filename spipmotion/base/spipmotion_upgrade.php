<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function spipmotion_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/spipmotion');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		if (version_compare($current_version,'0.0','<=')){
			creer_base();
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);
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
			maj_tables('spip_documents');
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
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.5);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.5)).'</p>';
		}
		if (version_compare($current_version,'0.6','<')){
			maj_tables('spip_spipmotion_attentes');
			ecrire_meta($nom_meta_base_version,$current_version=0.6);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.6)).'</p>';
		}
		if (version_compare($current_version,'0.7','<')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.7);
		}
		if (version_compare($current_version,'0.7.1','<')){
			/**
			 * Récupérer la configuration de FFMPEG sur le système et la mettre dans les métas
			 */
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);
			ecrire_meta($nom_meta_base_version,$current_version='0.7.1');
		}
		if (version_compare($current_version,'0.7.2','<')){
			/**
			 * On change le champs pixelformat
			 */
			sql_alter("TABLE spip_documents CHANGE `pixelformat` `pixelformat` VARCHAR(255) DEFAULT '' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version='0.7.2');
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