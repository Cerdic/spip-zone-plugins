<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 * @package SPIP\SPIPmotion\Fonctions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Transforme l'id d'un codec audio d'un fichier flv
 * en chaine de caractère lisible
 * 
 * @param int $type
 * 		Le numéro du codec
 * @return string 
 * 		La chaine de caractère lisible correspondant au codec
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
 * 
 * @param int $type
 * 		Le numéro du codec
 * @return string 
 * 		La chaine de caractère lisible
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

/**
 * Converti une durée en secondes en une durée affichable et lisible 
 * hh:mm:ss ou mm:ss
 * 
 * @param int|float $temps_secondes 
 * 		Le nombre de secondes
 * @return string $str
 * 		Le temps sous forme de chaîne de caractère
 */
function spipmotion_duree($temps_secondes){
	$diff_hours = floor($temps_secondes/3600);
	$temps_secondes -= $diff_hours * 3600;
	$diff_hours = (($diff_hours >= 0) && ($diff_hours < 10)) ? '0'.$diff_hours : $diff_hours;

	$diff_minutes = floor($temps_secondes/60);
	$temps_secondes -= $diff_minutes * 60;
	$diff_minutes = (($diff_minutes >= 0) && ($diff_minutes < 10)) ? '0'.$diff_minutes : $diff_minutes;

	$temps_secondes = (($temps_secondes >= 0) && ($temps_secondes < 10)) ? '0'.floor($temps_secondes) : floor($temps_secondes);

	$str = (($diff_hours > 0) ? $diff_hours.':':'').(($diff_minutes > 0) ? $diff_minutes:'00').':'.$temps_secondes;

	return $str;
}
?>