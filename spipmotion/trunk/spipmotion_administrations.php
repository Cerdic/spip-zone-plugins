<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function spipmotion_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/spipmotion');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		include_spip('inc/acces');
		if (version_compare($current_version,'0.0','<=')){
			creer_base();
			maj_tables('spip_documents');
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);

			$ffmpeg_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
			$ffmpeg_binaires('',true);
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			sql_alter("TABLE spip_spipmotion_attentes ADD `id_auteur` BIGINT(21) NOT NULL DEFAULT '0' AFTER `id_article`");
			sql_alter("TABLE spip_spipmotion_attentes ADD INDEX ( `id_auteur` )");
			ecrire_meta($nom_meta_base_version,$current_version=0.2);
		}
		if (version_compare($current_version,'0.3','<')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.3);
		}
		if (version_compare($current_version,'0.4','<')){
			sql_alter("TABLE spip_spipmotion_attentes CHANGE `id_article` `id_objet` BIGINT(21) NOT NULL DEFAULT '0'");
			sql_alter("TABLE spip_spipmotion_attentes ADD `objet` VARCHAR(25) AFTER `id_objet`");
			ecrire_meta($nom_meta_base_version,$current_version=0.4);
		}
		if (version_compare($current_version,'0.5','<')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.5);
		}
		if (version_compare($current_version,'0.6','<')){
			maj_tables('spip_spipmotion_attentes');
			ecrire_meta($nom_meta_base_version,$current_version=0.6);
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
		if (version_compare($current_version,'0.7.3','<')){
			/**
			 * On récupère les informations de spipmotion si possible
			 */
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);

			$ffmpeg_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
			$ffmpeg_binaires('',true);

			maj_tables('spip_spipmotion_attentes');
			/**
			 * On invalide le cache
			 */
			include_spip('inc/invalideur');
			suivre_invalideur("1");
			ecrire_meta($nom_meta_base_version,$current_version='0.7.3');
		}
		if (version_compare($current_version,'0.7.4','<')){
			sql_alter("TABLE spip_documents CHANGE `framerate` `framerate` FLOAT");
			ecrire_meta($nom_meta_base_version,$current_version='0.7.4');
		}
		if (version_compare($current_version,'0.7.5','<')){
			ecrire_meta($nom_meta_base_version,$current_version='0.7.5');
		}
		if (version_compare($current_version,'0.7.6','<')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version='0.7.6');
		}
		if (version_compare($current_version,'0.7.7','<')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version='0.7.7');
		}
		if (version_compare($current_version,'0.7.8','<')){
			ecrire_meta($nom_meta_base_version,$current_version='0.7.8');
		}if (version_compare($current_version,'0.7.9','<')){
			creer_base();
			effacer_meta('spipmotion_formats');
			effacer_meta('spipmotion_codecs');
			effacer_meta('spipmotion_codecs_audio_decode');
			effacer_meta('spipmotion_codecs_video_decode');
			effacer_meta('spipmotion_codecs_audio_encode');
			effacer_meta('spipmotion_codecs_video_encode');
			effacer_meta('spipmotion_bitstream_filters');
			effacer_meta('spipmotion_protocols');
			effacer_meta('spipmotion_avfilters');
			effacer_meta('spipmotion_compiler');
			effacer_meta('spipmotion_ffmpeg2theora');
			effacer_meta('spipmotion_flvtool2');
			effacer_meta('spipmotion_flvtoolplus');
			effacer_meta('spipmotion_mediainfo');
			effacer_meta('spipmotion_ffmpeg_casse');
			effacer_meta('spipmotion_ffmpeg2theora_casse');
			effacer_meta('spipmotion_flvtool_casse');
			effacer_meta('spipmotion_flvtoolplus_casse');
			effacer_meta('spipmotion_qt-faststart_casse');
			effacer_meta('spipmotion_spipmotionsh_casse');
			effacer_meta('spipmotion_ffmpeg-php_casse');
			effacer_meta('safe_mode');
			effacer_meta('spipmotion_safe_mode_exec_dir');
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);
			ecrire_meta($nom_meta_base_version,$current_version='0.7.9');
		}
		if (version_compare($current_version,'0.8.0','<')){
			/**
			 * On change le champs metas en metadatas
			 */
			sql_alter("TABLE spip_documents CHANGE `metas` `metadatas` TEXT DEFAULT '' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version='0.8.0');
		}
		/**
		 * TODO : générer un htaccess dans le répertoire script_bash/
		 * TODO : insérer une préconfiguration par défaut
		 */
	}
}

/**
 * 
 * Désinstallation du plugin
 * 
 * On supprime : 
 * -* Les metas de configuration
 * -* Les metas de vérification des programmes
 * -* La table spip_spipmotion_attentes
 * 
 * On laisse :
 * -* Les nouveaux champs sur la table spip_documents
 * 
 * @param float $nom_meta_base_version
 */
function spipmotion_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_spipmotion_attentes");
	sql_drop_table("spip_spipmotion_metas");
	effacer_meta($nom_meta_base_version);
}
?>