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