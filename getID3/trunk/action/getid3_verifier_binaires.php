<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');

/**
 * Action de vérification des binaires
 */
function action_getid3_verifier_binaires_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('inc/autoriser');

	if(autoriser('configurer')){
		$verifier_binaires = charger_fonction('getid3_verifier_binaires','inc');
		$verifier_binaires(true);
	}
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>