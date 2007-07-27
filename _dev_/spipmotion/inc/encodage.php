<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// Faire l'encodage d'une video en .flv
//
include_spip('inc/charsets'); # pour le nom de fichier
include_spip('base/abstract_sql');
include_spip('inc/actions');

function inc_encodage_dist ($sources, $file, $type, $id, $mode, $id_document, &$actifs, $hash='', $redirect='', $iframe_redirect='')
{
	  return encodage($sources, $file, $type, $id, $mode, $id_document, $actifs);
}

function encodage($sources, $file, $type_lien, $id_lien, $mode, $id_document, &$documents_actifs){
	$dir_ftp = determine_upload();
	spip_log ("encodage de la video $dir_ftp$file  (M '$mode' T '$type_lien' L '$id_lien' D '$id_document')");
	$encodageflv = _DIR_PLUGIN_SPIPMOTION.'script_bash/spipmotion.sh --e '.$dir_ftp.$file.' --s '.$dir_ftp.$file.'.flv --bitrate '.lire_config("spipmotion/bitrate","448").' --audiobitrate '.lire_config("spipmotion/bitrate_audio","64").' --audiofreq '. lire_config("spipmotion/frequence_audio","22050").' --fps '.lire_config("spipmotion/fps","15").' --p '.lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg");
	$x = shell_exec($encodageflv);
	return $x;
}
?>