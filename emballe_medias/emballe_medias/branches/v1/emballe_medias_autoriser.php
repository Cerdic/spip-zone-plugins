<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Fichier des autorisations
 **/

 if (!defined("_ECRIRE_INC_VERSION")) return;
 
function emballe_medias_autoriser(){}

/**
 * Autorisation à afficher le sélecteur de fichier depuis le FTP
 * 
 * @param string $faire
 * @param string $type
 * @param id $id
 * @param array $qui
 * @param array $opt
 */
function autoriser_em_chargerftp_dist($faire, $type, $id, $qui, $opt){
	include_spip('formulaires/em_charger_media_ftp');
	if(!$opt){
		return autoriser('chargerftp');
	}else{
		include_spip('inc/actions');
		if ($dir = determine_upload('documents')) {
			$mode = $opt['mode'] ? $opt['mode'] : 'document';
			$extensions = $opt['extensions'] ? $opt['extensions'] : null;
			$max = $opt['max'] ? $opt['max'] : null;
			// quels sont les docs accessibles en ftp ?
			$options_ftp = joindre_options_upload_ftp($dir,$mode, $extensions,$max);
			if($options_ftp && autoriser('chargerftp')){
				return true;
			}else{
				return false;
			}
		}
	}
}

?>