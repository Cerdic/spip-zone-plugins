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

if (!defined('_ECRIRE_INC_VERSION')) return;

function spipmotion_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_documents','spip_spipmotion_metas')),
		array('spipmotion_install_recuperer_infos',array()),
		array('spipmotion_conf_base',array())
	);
	$maj['0.3'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.5'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.7'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.7.1'] = array(
		array('spipmotion_install_recuperer_infos',array()),
	);
	$maj['0.7.2'] = array(
		array('sql_alter','TABLE spip_documents CHANGE `pixelformat` `pixelformat` VARCHAR(255) DEFAULT "" NOT NULL')
	);
	$maj['0.7.3'] = array(
		array('spipmotion_install_recuperer_infos',array())
	);
	$maj['0.7.4'] = array(
		array('sql_alter','TABLE spip_documents CHANGE `framerate` `framerate` FLOAT')
	);
	$maj['0.7.6'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.7.7'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.7.9'] = array(
		array('maj_tables',array('spip_documents','spip_spipmotion_metas')),
		array('spipmotion_install_recuperer_infos',array()),
	);
	$maj['0.8.0'] = array(
		array('sql_alter','TABLE spip_documents CHANGE `metas` `metadatas` TEXT DEFAULT "" NOT NULL')
	);
	$maj['1.1.0'] = array(
		array('spipmotion_peuple_facd',array())
	);
	$maj['1.1.1'] = array(
		array('spipmotion_remove_idorig',array())
	);
	$maj['1.1.2'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['1.1.3'] = array(
		array('sql_alter','TABLE spip_documents CHANGE `metadatas` `metadatas` LONGTEXT NOT NULL'),
	);
	$maj['1.1.4'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['1.1.5'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['1.1.6'] = array(
		array('spipmotion_remove_idorig',array())
	);
	$maj['1.2.9'] = array(
		array('spipmotion_install_recuperer_infos',array()),
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
 * 
 * On laisse :
 * -* Les nouveaux champs sur la table spip_documents
 * 
 * @param float $nom_meta_base_version
 */
function spipmotion_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_spipmotion_metas");
	effacer_meta('spipmotion');
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
	$ffmpeg_infos = charger_fonction('spipmotion_ffmpeg_infos','inc');
	$ffmpeg_infos(true);

	$ffmpeg_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
	$ffmpeg_binaires('',true);
	
	/**
	 * On invalide le cache
	 */
	include_spip('inc/invalideur');
	suivre_invalideur("1");
}

/**
 * On peuple facd avant suppression de spip_spipmotion_attentes
 * 
 * On fait attention à renommer les deux éléments debut_conversion et fin_conversion
 */
function spipmotion_peuple_facd(){
	$res = sql_select("*","spip_spipmotion_attentes");
	while($row = sql_fetch($res)){
		$infos = @unserialize($row['infos']);
		if(is_array($infos)){
			$infos['debut_conversion'] = $infos['debut_encodage'];
			unset($infos['debut_encodage']);
			$infos['fin_conversion'] = $infos['fin_encodage'];
			unset($infos['fin_encodage']);
			$infos = serialize($infos);
		}
		sql_insertq('spip_facd_conversions',array(
												'id_auteur' => $row['id_auteur'],
												'id_document' => $row['id_document'],
												'statut' => $row['encode'],
												'fonction'=> 'spipmotion_encodage',
												'extension'=>$row['extension'],
												'infos'=> $infos,
												'maj'=>$row['maj']));
		sql_delete('spip_spipmotion_attentes','id_spipmotion_attente='.intval($row['id_spipmotion_attente']));
		if (time() >= _TIME_OUT)
			return;
	}
	sql_drop_table('spip_spipmotion_attentes');
}

/**
 * On vérifie si on a id_orig dans la table spip_documents et
 * on transforme tous les documents ayant id_orig > 0 en documents liés au document original
 * On enlève également le lien à l'ancien article
 */
function spipmotion_remove_idorig(){
	$desc = sql_showtable('spip_documents', true, $connect);
	if (is_array($desc['field']) && isset($desc['field']['id_orig'])) {
		$res = sql_select("*","spip_documents","id_orig > 0");
		while($row = sql_fetch($res)){
			sql_delete('spip_documents_liens','id_document='.intval($row['id_document']).' AND objet!= "document"');
			sql_insertq('spip_documents_liens',array(
													'id_document' => $row['id_document'],
													'objet' => 'document',
													'id_objet' => $row['id_orig']));
			sql_updateq('spip_documents',array('id_orig'=>0,'mode'=>'conversion'),'id_document='.intval($row['id_document']));
			if (time() >= _TIME_OUT)
				return;
		}
		sql_alter('TABLE spip_documents DROP id_orig');
	}
}

/**
 * Insertion d'une configuration de base
 */
 
function spipmotion_conf_base(){
	include_spip('inc/config');
	if(count(lire_config('spipmotion/fichiers_audios')) == 0){
		$config = array(
			'fichiers_audios' => array('3ga','aac','ac3','aifc','aiff','flac','m4a','mka','mp3','oga','ogg','wav','wma'),
			'fichiers_videos' => array('3gp','avi','dv','f4v','flv','m2p','m2ts','m4v','mkv','mpg','mov','mp4','mts','ogv','qt','ts','webm','wmv','y4m'),
			'fichiers_audios_encodage' => array('3ga','aac','ac3','aifc','aiff','flac','m4a','mka','mp3','oga','ogg','wav','wma'),
			'fichiers_videos_encodage' => array('3gp','avi','dv','f4v','flv','m2p','m2ts','m4v','mkv','mpg','mov','mp4','mts','ogv','qt','ts','webm','wmv','y4m'),
			'fichiers_audios_sortie' => array('mp3','ogg'),
			'fichiers_videos_sortie' => array('mp4','ogv','webm'),
			'frequence_audio_ogg' => '44100',
			'frequence_audio_ogv' => '44100',
			'frequence_audio_mp3' => '44100',
			'frequence_audio_mp4' => '44100',
			'frequence_audio_webm' => '44100',
			'bitrate_audio_mp3' => '128',
			'bitrate_audio_mp4' => '128',
			'bitrate_ogv' => '600',
			'bitrate_mp4' => '660',
			'bitrate_webm' => '660',
			'width_ogv' => '640',
			'width_mp4' => '640',
			'width_webm' => '640',
			'height_mp4' => '480',
			'height_ogv' => '480',
			'height_webm' => '480',
			'qualite_audio_ogg' => '5',
			'qualite_audio_ogv' => '5',
			'qualite_audio_webm' => '5',
			'acodec_mp3' => 'libmp3lame',
			'acodec_ogg' => 'libvorbis',
			'acodec_ogv' => 'libvorbis',
			'acodec_webm' => 'libvorbis',
			'acodec_mp4' => 'libfaac',
			'vcodec_ogv' => 'libtheora',
			'vcodec_webm' => 'libvpx',
			'vcodec_mp4' => 'libx264',
			'format_mp4' => 'ipod',
			'vpreset_mp4' => 'slow',
			'passes_ogv' => '2',
			'passes_mp4' => '2',
			'passes_webm' => '2'
		);

		ecrire_meta('spipmotion',serialize($config));
	}
}
?>