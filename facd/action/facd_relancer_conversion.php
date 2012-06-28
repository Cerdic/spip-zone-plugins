<?php
/**
 * FACD
 * File d'Attente de Conversion de Documents
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');

/**
 * Relancer un encodage en erreur
 */
function action_facd_relancer_conversion_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('inc/autoriser');

	$update = 'nok';
	if(is_numeric($arg) && autoriser('relancerconversion','facd',$arg) &&
		(sql_getfetsel('statut','spip_facd_conversions','id_facd_conversion='.intval($arg)) == 'erreur')){
		sql_updateq('spip_facd_conversions',array('statut'=>'non'),'id_facd_conversion='.intval($arg));
		$update = 'ok';
	}else if(($arg == 'tout') && autoriser('configurer','facd')){
		sql_updateq('spip_facd_conversions',array('statut'=>'non'),'statut="erreur"');
		$update = 'ok_tout';
	}
	
	$conversion_directe = charger_fonction('facd_convertir_direct','inc');
	$conversion_directe();
	
	if(_request('redirect')){
		$redirect = urldecode(_request('redirect'));
		//redirige_par_entete(parametre_url($redirect,'relance',$update,'&'));
	}
	return $redirect;
}
?>