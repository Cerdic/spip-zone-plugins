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

function inc_encodage($files){

$dir_ftp = determine_upload();
$page_save_conf = _DIR_PLUGIN_SPIPMOTION."/inc/spipmotion_conf.php";
include($page_save_conf);

	foreach ($files as $arg) {
		$encodageflv = _DIR_PLUGIN_SPIPMOTION.'script_bash/spipmotion.sh --e '.$dir_ftp.$arg['name'].' --s '.$dir_ftp.$arg['name'].'.flv --bitrate '.$bitrate.' --audiobitrate '.$bitrate_audio.' --audiofreq '. $frequence_audio.' --fps '.$fps.' --p '.$chemin;
		$x = shell_exec($encodageflv);
		spip_log($encodageflv);
	}
	return $x;
}
?>