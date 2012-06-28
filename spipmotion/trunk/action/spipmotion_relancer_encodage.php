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
 * Relancer un encodage en erreur
 */
function action_spipmotion_relancer_encodage_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('inc/autoriser');

	$update = 'nok';
	if(is_numeric($arg) && autoriser('relancerencodage','spipmotion',$arg) &&
		(sql_getfetsel('encode','spip_spipmotion_attentes','id_spipmotion_attente='.intval($arg)) == 'erreur')){
		sql_updateq('spip_spipmotion_attentes',array('encode'=>'non'),'id_spipmotion_attente='.intval($arg));
		$update = 'ok';
	}else if(($arg == 'tout') && autoriser('configurer','spipmotion')){
		sql_updateq('spip_spipmotion_attentes',array('encode'=>'non'),'encode="erreur"');
		$update = 'ok_tout';
	}
	
	$conversion_directe = charger_fonction('fact_convertir_direct','inc');
	$conversion_directe();
	
	if(_request('redirect')){
		$redirect = urldecode(_request('redirect'));
		//redirige_par_entete(parametre_url($redirect,'relance',$update,'&'));
	}
	return $redirect;
}
?>