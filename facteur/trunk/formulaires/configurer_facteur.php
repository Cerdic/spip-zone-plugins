<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Formulaires\Configurer_facteur
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_facteur_cles_password_masques() {
	return array(
		'smtp_password',
		'mailjet_secret_key',
	);
}

function formulaires_configurer_facteur_charger_dist() {

	include_spip('inc/cvt_configurer');

	$valeurs = cvtconf_formulaires_configurer_recense('configurer_facteur');
	$valeurs['editable'] = true;

	foreach(formulaires_configurer_facteur_cles_password_masques() as $_key){
		$valeurs['_'.$_key] = $valeurs[$_key];
		$valeurs[$_key] = '';
	}

	// recuperer le from par defaut actuel pour l'indiquer dans le formulaire
	include_spip('inc/facteur');
	$from_defaut = facteur_config_envoyeur_par_defaut();
	$valeurs['_from_defaut'] = $from_defaut['adresse_envoi_email'];
	$valeurs['_from_defaut_nom'] = '';
	$valeurs['_from_defaut_email'] = $from_defaut['adresse_envoi_email'];
	if (!empty($from_defaut['adresse_envoi_nom'])) {
		$valeurs['_from_defaut'] = $from_defaut['adresse_envoi_nom'] . ' &lt;'.$valeurs['_from_defaut'].'&gt;';
		$valeurs['_from_defaut_nom'] = $from_defaut['adresse_envoi_nom'];
	}

	if (defined('_TEST_EMAIL_DEST')) {
		if (_TEST_EMAIL_DEST) {
			$valeurs['_message_warning'] = _T('facteur:info_envois_forces_vers_email', array('email' => _TEST_EMAIL_DEST));
		}
		else {
			$valeurs['_message_warning'] = _T('facteur:info_envois_bloques_constante');
		}
	}

	return $valeurs;
}

function formulaires_configurer_facteur_verifier_dist() {
	$erreurs = array();
	include_spip('inc/config');
	if ($email = _request('adresse_envoi_email')
		and !email_valide($email)) {
		$erreurs['adresse_envoi_email'] = _T('form_email_non_valide');
		set_request('adresse_envoi', 'oui');
	}
	$mailer = _request('mailer');
	if (function_exists($verifier_mailer = 'formulaires_configurer_facteur_verifier_' . $mailer)) {
		$verifier_mailer($erreurs);
	}
	if ($emailcc = _request('cc')
		and !email_valide($emailcc)) {
		$erreurs['cc'] = _T('form_email_non_valide');
	}
	if ($emailbcc = _request('bcc')
		and !email_valide($emailbcc)) {
		$erreurs['bcc'] = _T('form_email_non_valide');
	}

	if (count($erreurs) > 0) {
		$erreurs['message_erreur'] = _T('facteur:erreur_generale');
	}
	return $erreurs;
}

/**
 * Verifier la configuration du smtp si besoin
 * @param $erreurs
 */
function formulaires_configurer_facteur_verifier_smtp(&$erreurs){
	if (!($h = _request('smtp_host'))) {
		$erreurs['smtp_host'] = _T('info_obligatoire');
	} else {
		$h = trim($h);
		$regexp_ip_valide = '#^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))|((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$#';
		// Source : http://www.d-sites.com/2008/10/09/regex-ipv4-et-ipv6/
		if (!preg_match($regexp_ip_valide, $h)) { // ce n'est pas une IP
			if (!preg_match(';^([^.\s/?:]+[.])*[^.\s/?:]+$;', $h)
				or gethostbyname($h) == $h) {
				$erreurs['smtp_host'] = _T('facteur:erreur_invalid_host');
			}
		} else {
			if (gethostbyaddr($h) == $h) {
				$erreurs['smtp_host'] = _T('facteur:erreur_invalid_host');
			}
		}
		set_request('smtp_host', $h);
	}
	if (!($p=_request('smtp_port'))) {
		$erreurs['smtp_port'] = _T('info_obligatoire');
	} elseif (!preg_match(';^[0-9]+$;', $p) or !intval($p)) {
		$erreurs['smtp_port'] = _T('facteur:erreur_invalid_port');
	}

	if (!_request('smtp_auth')) {
		$erreurs['smtp_auth'] = _T('info_obligatoire');
	}

	if (_request('smtp_auth')=='oui') {
		if (!_request('smtp_username')) {
			$erreurs['smtp_username'] = _T('info_obligatoire');
		}
		if (!_request('smtp_password') and !lire_config('facteur/smtp_password')) {
			$erreurs['smtp_password'] = _T('info_obligatoire');
		}
	}
}

function formulaires_configurer_facteur_traiter_dist() {
	include_spip('inc/config');

	// reinjecter les password pas saisis si besoin
	$restore_after_save = array();
	foreach(formulaires_configurer_facteur_cles_password_masques() as $_key){
		if (!_request($_key)){
			$restore_after_save[$_key] = '';
			set_request($_key,lire_config('facteur/'.$_key));
		}
	}

	include_spip('inc/cvt_configurer');
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_facteur', array());
	$res = array(
		'editable' => true
	);
	include_spip('inc/facteur');
	try {
		$facteur = facteur_factory(array('exceptions' => true));
		$facteur->configure();
		$res['message_ok'] = _T('facteur:config_info_enregistree') . $trace;
	}
	catch (Exception $e) {
		$res['message_erreur'] = $e->getMessage();
	}

	foreach($restore_after_save as $k=>$v){
		set_request($k,$v);
	}

	return $res;
}
