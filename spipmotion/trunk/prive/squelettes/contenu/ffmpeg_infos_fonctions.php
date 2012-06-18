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
	$infos_ffmpeg = charger_fonction('ffmpeg_infos','inc');
	$infos = $infos_ffmpeg();
	return $infos;
}
?>