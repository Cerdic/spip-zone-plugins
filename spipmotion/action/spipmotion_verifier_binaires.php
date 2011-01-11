<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2011 - Distribué sous licence GNU/GPL
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
		$verifier_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
		$verifier_binaires('',true);
	}
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		redirige_par_entete($redirect);
	}
}
?>