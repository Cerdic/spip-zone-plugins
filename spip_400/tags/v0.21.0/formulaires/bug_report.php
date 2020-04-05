<?php

function formulaires_bug_report_charger_dist($code_erreur='404', $url=false){ 
	return array(
		'url_bug' => $url,
		'code_erreur' => $code_erreur,
	);
}

function formulaires_bug_report_verifier_dist($code_erreur='404', $url=false){}

function formulaires_bug_report_traiter_dist($code_erreur='404', $url=false){ 
	$messages = array();
	if (!$url) $url = spip_400_self();
	$typemail = 'bug';
	include_spip('inc/texte');
	$sitename = couper($GLOBALS['meta']['nom_site'], 10, '.');

	$sujet = _T('spip_400:report_a_bug_titre_mail', array('code'=>$code_erreur, 'sitename'=>$sitename));
	$texte = _T('spip_400:report_a_bug_texte_mail', array(
		'code'=>$code_erreur, 'url'=>$url, 'date'=>date('Y/m/d - H:i:s'),
	));

	// Config ou pas ?
	$mail_sender = $mail_receipt = $GLOBALS['meta']['email_webmaster'];
	if (function_exists('lire_config')) {
		$cfg_400 = lire_config('spip_400');
		if ($cfg_400 && isset($cfg_400['sender_400'])) {
			$mail_sender = $cfg_400['sender_400'];
		}
		if ($cfg_400 && isset($cfg_400['receipt_400'])) {
			$mail_receipt = $cfg_400['receipt_400'];
		}
	}

	// Si utilisateur
	if (isset($GLOBALS["visiteur_session"]) && isset($GLOBALS["visiteur_session"]['id_auteur'])) {
		if ($code_erreur=='401') {
			$typemail = 'auth';
			$texte = _T('spip_400:request_auth_texte_mail', array(
				'code'=>$code_erreur, 'url'=>$url, 'date'=>date('Y/m/d - H:i:s'), 'user' => $GLOBALS["visiteur_session"]['nom'],
			));
		}
		$texte .= "\n\n-- "._T('spip_400:utilisateur_concerne').$GLOBALS["visiteur_session"]['nom'].' ('.$GLOBALS["visiteur_session"]['email'].')';
	}

	// Infos URL complete
	$texte .= "\n\n-- "._T('spip_400:url_complete')." : ".$url;
	if (isset($_SERVER['HTTP_REFERER']))	
		$texte .= "\n\n-- "._T('spip_400:referer')." : ".$_SERVER['HTTP_REFERER'];
	file_get_contents($url);
	if (isset($http_response_header) && is_array($http_response_header) && count($http_response_header)) {
		$texte .= "\n\n-- "._T('spip_400:http_headers');
		foreach($http_response_header as $var=>$val)
			$texte .= "\n".' #'.$val;
		$texte .= "\n---- ";
	}

	// Session
	if (isset($GLOBALS["visiteur_session"])) {
		$session_str = '';
		foreach($GLOBALS["visiteur_session"] as $sess_var=>$sess_val){
			if ($sess_val && strlen($sess_val)>0)
				$session_str .= "\n".'#'.$sess_var.' => '.$sess_val;
		}
		$texte .= "\n\n-- "._T('spip_400:session')."\n"._T('spip_400:session_only_notempty_values').$session_str."\n---- ";
	}

	// Backtrace PHP
	@ob_start();
	@debug_print_backtrace();
	$backtrace = @ob_get_contents();
	@ob_end_clean();
	if ($backtrace)
		$texte .= "\n\n-- "._T('spip_400:backtrace')."\n".$backtrace."\n---- ";

	// Pied: info site
	$texte .= "\n\n-- "._T('envoi_via_le_site')
		." ".supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']))
		." (".$GLOBALS['meta']['adresse_site']."/) --\n";

	// Envoi puis retour
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	if ($envoyer_mail($mail_receipt, $sujet, $texte, $mail_sender, "X-Originating-IP: ".$GLOBALS['ip'])) {
		if ($typemail=='auth') {
			$messages['message_ok'] = _T('spip_400:request_auth_message_envoye');
		} else {
			$messages['message_ok'] = _T('spip_400:report_a_bug_message_envoye');
		}
	} else {
		$messages['message_erreur'] = _T('pass_erreur_probleme_technique');
	}

	// Debug
//	echo "<pre>"; echo "<br />titre : $sujet"; echo "<br />texte : ".var_export($texte,1); exit;
	return $messages;
}
?>