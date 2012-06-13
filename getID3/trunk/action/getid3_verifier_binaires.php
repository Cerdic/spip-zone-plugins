<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1), BoOz
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');

/**
 * Action de vérification des binaires
 */
function action_getid3_verifier_binaires_dist(){
	global $visiteur_session;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('inc/autoriser');

	if(autoriser('configurer','',$visiteur_session)){
		$verifier_binaires = charger_fonction('getid3_verifier_binaires','inc');
		$verifier_binaires(true);
	}
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		redirige_par_entete($redirect);
	}
}
?>