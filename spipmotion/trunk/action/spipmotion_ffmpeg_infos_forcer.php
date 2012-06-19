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

function action_spipmotion_ffmpeg_infos_forcer_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$ffmpeg_infos = charger_fonction('spipmotion_ffmpeg_infos','inc');
	$ffmpeg_infos(true);

	/**
	 * On invalide le cache
	 */
	include_spip('inc/invalideur');
	suivre_invalideur("1");

	if(_request('redirect')){
		$redirect = parametre_url(str_replace('&amp;','&',urldecode(_request('redirect'))),'maj_infos','ok','&');
		redirige_par_entete($redirect);
	}else{
		redirige_par_entete(parametre_url(self(),'maj_infos','ok','&'));
	}
}

?>