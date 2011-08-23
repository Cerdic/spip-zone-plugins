<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2011 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Transforme un type de codec donné par la config de ffmpeg
 * (ffmpec -codecs) en chaine de caractère lisible
 * @param string $type
 * @return string La chaine de caractère lisible
 */
function ffmpeg_codec_type_to_type($type=null){
	$trans = array(
		'A' => _T('spipmotion:codec_type_audio'),
		'V' => _T('spipmotion:codec_type_video'),
		'S' => _T('spipmotion:codec_type_soustitre')
	);

	return $trans[$type] ? $trans[$type] : $type;
}

/**
 * Transforme l'id d'un codec audio d'un fichier flv
 * en chaine de caractère lisible
 * @param string $type
 * @return string La chaine de caractère lisible
 */
function flv_audio_codec_type_to_type($type=null){
	$trans = array(
		'0'=>'Uncompressed',
		'1'=>'ADPCM',
		'2'=>'Mp3',
		'4'=>'Nellymoser 16kHz Mono',
		'5'=>'Nellymoser 8kHz Mono',
		'6'=>'Nellymoser',
		'10'=>'AAC',
		'11'=>'Speex'
	);
	return $trans[$type] ? $trans[$type] : $type;
}

/**
 * Transforme l'id d'un codec video d'un fichier flv
 * en chaine de caractère lisible
 * @param string $type
 * @return string La chaine de caractère lisible
 */
function flv_video_codec_type_to_type($type=null){
	$trans = array(
		'2'=>'Sorenson H.263',
		'3'=>'Screen Video',
		'4'=>'On2 VP6',
		'5'=>'On2 VP6 Transparency'
	);
	return $trans[$type] ? $trans[$type] : $type;
}
?>