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

function infos_ffmpeg(){
	$infos_ffmpeg = charger_fonction('spipmotion_ffmpeg_infos','inc');
	$infos = $infos_ffmpeg();
	return $infos;
}

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
?>