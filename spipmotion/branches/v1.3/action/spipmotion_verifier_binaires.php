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

include_spip('inc/actions');

/**
 * Action de vérification des binaires
 */
function action_spipmotion_verifier_binaires_dist(){
	global $visiteur_session;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('inc/autoriser');

	if(autoriser('configurer','',$visiteur_session)){
		$ffmpeg_infos = charger_fonction('spipmotion_ffmpeg_infos','inc');
		$ffmpeg_infos(true);
	}
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		redirige_par_entete($redirect);
	}
}
?>