<?php
/*
 * Plugin Mailjet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_mailjet_charger_dist(){

	include_spip('inc/autoriser');
	if (!autoriser('configurer'))
		return _T('mailjet:configuration_acces_interdit');

	$valeurs = array(
		'mailjet_enabled' => isset($GLOBALS['meta']['mailjet_enabled'])?$GLOBALS['meta']['mailjet_enabled']:true,
		'mailjet_username' => isset($GLOBALS['meta']['mailjet_username'])?$GLOBALS['meta']['mailjet_username']:'',
		'mailjet_password' => isset($GLOBALS['meta']['mailjet_password'])?$GLOBALS['meta']['mailjet_password']:'',
	);

	return $valeurs;
}

function formulaires_configurer_mailjet_verifier_dist(){
	$erreurs = array();

	if (_request('mailjet_enabled')){
		foreach (array('mailjet_username','mailjet_password') as $obli){
			if (!_request($obli))
				$erreurs[$obli] = _T('info_obligatoire');
		}
	}
	// sinon 0 dans mailjet_enabled
	else
		set_request('mailjet_enabled',0);

	return $erreurs;
}

function formulaires_configurer_mailjet_traiter_dist(){

	$res = array('editable'=>true);
	$enabled = _request('mailjet_enabled');
	ecrire_meta('mailjet_enabled',$enabled);
	if ($enabled){
		foreach (array('mailjet_username','mailjet_password') as $config){
			ecrire_meta($config,_request($config));
		}
		// detecter le port
		$configs = array (
			array ('ssl://', 465),
			array ('tls://', 587),
			array ('', 587),
			array ('', 588),
			array ('tls://', 25),
			array ('', 25)
		);

		$host = 'in.mailjet.com';
		$connected = FALSE;

		$errno = $errstr = "";
		for ($i = 0; $i < count ($configs); ++$i){
			$soc = @fSockOpen ($configs[$i][0].$host, $configs[$i][1], $errno, $errstr, 5);
			if ($soc){
				fClose($soc);
				$connected = TRUE;
				break;
			}
		}
		if ($connected) {
			ecrire_meta('mailjet_host', $configs[$i][0].$host);
			ecrire_meta('mailjet_port', $configs[$i][1]);
			$res['message_ok'] = _T('config_info_enregistree');
			$res['message_ok'] .= "<br />"._T('mailjet:mj_autoconfig_host_port',array('host'=>"<tt>".$GLOBALS['meta']['mailjet_host']."</tt>",'port'=>$GLOBALS['meta']['mailjet_port']));
			if (_request('tester')){
				mailjet_envoyer_mail_test($GLOBALS['meta']['email_webmaster'],_T('mailjet:mailjet_titre_email_test'));
				$res['message_ok'] .= "<br />"._T('mailjet:message_envoi_test',array('email'=>$GLOBALS['meta']['email_webmaster']));
			}
		}
		else {
			unset($res['message_ok']);
			$res['message_erreur'] = _T('mailjet:mj_error_autoconfig',array('no'=>$errno,'str'=>$errstr));
		}
	}
	else
		$res['message_ok'] = _T('config_info_enregistree');

	return $res;
}

function mailjet_envoyer_mail_test($destinataire,$titre){
	$message_html	= recuperer_fond('mailjet/emails/test_email_html', array());
	$message_texte	= recuperer_fond('mailjet/emails/test_email_texte', array());

	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($destinataire, $titre, array('html'=>$message_html,'texte'=>$message_texte));
}
