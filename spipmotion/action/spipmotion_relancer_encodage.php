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
 * Relancer un encodage en erreur
 */
function action_spipmotion_relancer_encodage_dist(){
	global $visiteur_session;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	spip_log($arg,'test');

	include_spip('inc/autoriser');

	$update = 'nok';
	if(is_numeric($arg) && autoriser('relancerencodage','spipmotion',$arg,$visiteur_session) &&
		(sql_getfetsel('encode','spip_spipmotion_attentes','id_spipmotion_attente='.intval($arg)) == 'erreur')){
		sql_updateq('spip_spipmotion_attentes',array('encode'=>'non'),'id_spipmotion_attente='.intval($arg));
		$update = 'ok';
	}else if(($arg == 'tout') && autoriser('configurer','','',$visiteur_session)){
		sql_updateq('spip_spipmotion_attentes',array('encode'=>'non'),'encode="erreur"');
		$update = 'ok_tout';
	}
	
	$encodage_direct = charger_fonction('spipmotion_encodage_direct','inc');
	$encodage_direct();
	
	if(_request('redirect')){
		$redirect = urldecode(_request('redirect'));
		redirige_par_entete(parametre_url($redirect,'relance',$update,'&'));
	}
}
?>