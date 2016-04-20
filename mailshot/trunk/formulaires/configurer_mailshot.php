<?php
/*
 * Plugin Facteur 2
 * (c) 2009-2011 Collectif SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_mailshot_charger_dist(){
	include_spip('inc/cvt_configurer');

	$valeurs = cvtconf_formulaires_configurer_recense('configurer_mailshot');
	$valeurs['editable'] = true;

	$valeurs['_smtp_password'] = $valeurs['smtp']['password'];
	$valeurs['smtp']['password'] = '';

	$valeurs['_mailjet_secret_key'] = $valeurs['mailjet_secret_key'];
	$valeurs['mailjet_secret_key'] = '';

	$valeurs['_mandrill_api_key'] = $valeurs['mandrill_api_key'];
	$valeurs['mandrill_api_key'] = '';

	return $valeurs;
}

function formulaires_configurer_mailshot_verifier_dist(){

	$erreurs = array();
	return $erreurs;
}

function formulaires_configurer_mailshot_traiter_dist(){
	// reinjecter les password pas saisis si besoin
	$restore_after_save = array();
	if ($smtp = _request('smtp') AND !$smtp['password']){
		$restore_after_save['smtp'] = $smtp;
		$smtp['password'] = lire_config('mailshot/smtp/password');
		set_request('smtp',$smtp);
	}
	if (!_request('mailjet_secret_key')){
		$restore_after_save['mailjet_secret_key'] = '';
		set_request('mailjet_secret_key',lire_config('mailshot/mailjet_secret_key'));
	}
	if (!_request('mandrill_api_key')){
		$restore_after_save['mandrill_api_key'] = '';
		set_request('mandrill_api_key',lire_config('mailshot/mandrill_api_key'));
	}

	include_spip('inc/cvt_configurer');
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_mailshot', array());
	$res = array('message_ok' => _T('config_info_enregistree') . $trace, 'editable' => true);

	foreach($restore_after_save as $k=>$v){
		set_request($k,$v);
	}

	return $res;
}
