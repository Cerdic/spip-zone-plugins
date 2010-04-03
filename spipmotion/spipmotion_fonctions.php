<?php

/**
 * Transforme un type de codec donné par la config de ffmpeg
 * (ffmpec -codecs) en chaine de caractère lisible
 * @param string $type
 * @return string La chaine de caractère lisible
 */
function codec_type_to_type($type=null){
	$trans = array(
		'A' => _T('spipmotion:codec_type_audio'),
		'V' => _T('spipmotion:codec_type_video'),
		'S' => _T('spipmotion:codec_type_soustitre')
	);

	return $trans[$type] ? $trans[$type] : $type;
}
?>