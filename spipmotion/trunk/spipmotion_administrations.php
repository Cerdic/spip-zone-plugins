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
		array('spipmotion_install_recuperer_infos',array())
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
		array('sql_alter',array('TABLE spip_documents CHANGE `pixelformat` `pixelformat` VARCHAR(255) DEFAULT "" NOT NULL'))
	);
	$maj['0.7.3'] = array(
		array('spipmotion_install_recuperer_infos',array())
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
		array('maj_tables',array('spip_documents','spip_spipmotion_metas')),
		array('spipmotion_install_recuperer_infos',array()),
	);
	$maj['0.8.0'] = array(
		array('sql_alter',array('TABLE spip_documents CHANGE `metas` `metadatas` TEXT DEFAULT "" NOT NULL'))
	);
	$maj['1.1.0'] = array(
		array('spipmotion_peuple_facd',array())
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
?>