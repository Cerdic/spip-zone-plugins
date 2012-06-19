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
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_documents','spip_spipmotion_metas','spip_spipmotion_attentes')),
		array('spipmotion_install_recuperer_infos',array())
	);
	$maj['0.2'] = array(
		array('sql_alter',array('TABLE spip_spipmotion_attentes ADD `id_auteur` BIGINT(21) NOT NULL DEFAULT "0" AFTER `id_article`')),
		array('sql_alter',array('TABLE spip_spipmotion_attentes ADD INDEX ( `id_auteur` )'))
	);
	$maj['0.3'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.4'] = array(
		array('sql_alter',array('TABLE spip_spipmotion_attentes CHANGE `id_article` `id_objet` BIGINT(21) NOT NULL DEFAULT "0"')),
		array('sql_alter',array('TABLE spip_spipmotion_attentes ADD `objet` VARCHAR(25) AFTER `id_objet`'))
	);
	$maj['0.5'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.6'] = array(
		array('maj_tables',array('spip_spipmotion_attentes')),
	);
	$maj['0.7'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.7.1'] = array(
		array('spipmotion_install_recuperer_infos',array()),
	);
	$maj['0.7.2'] = array(
		array('sql_alter',array('TABLE spip_documents CHANGE `pixelformat` `pixelformat` VARCHAR(255) DEFAULT "" NOT NULL'))
	);
	$maj['0.7.3'] = array(
		array('maj_tables',array('spip_spipmotion_attentes')),
		array('spipmotion_install_recuperer_infos',array()),
	);
	$maj['0.7.4'] = array(
		array('sql_alter',array('TABLE spip_documents CHANGE `framerate` `framerate` FLOAT'))
	);
	$maj['0.7.6'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.7.7'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.7.9'] = array(
		array('maj_tables',array('spip_documents','spip_spipmotion_metas','spip_spipmotion_attentes')),
		array('spipmotion_install_recuperer_infos',array()),
	);
	$maj['0.8.0'] = array(
		array('sql_alter',array('TABLE spip_documents CHANGE `metas` `metadatas` TEXT DEFAULT "" NOT NULL'))
	);
	/**
	 * TODO : générer un htaccess dans le répertoire script_bash/
	 * TODO : insérer une préconfiguration par défaut
	 */
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
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

function spipmotion_install_recuperer_infos(){
	/**
	 * On vire ces metas qui peuvent exister
	 */
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
	
	/**
	 * On récupère les informations de spipmotion si possible
	 */
	$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
	$ffmpeg_infos(true);

	$ffmpeg_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
	$ffmpeg_binaires('',true);
	
	/**
	 * On invalide le cache
	 */
	include_spip('inc/invalideur');
	suivre_invalideur("1");
}
?>