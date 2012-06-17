<?php

function infos_ffmpeg(){
	$infos_ffmpeg = charger_fonction('ffmpeg_infos','inc');
	$infos = $infos_ffmpeg();
	return $infos;
}
?>