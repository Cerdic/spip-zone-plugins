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

function formulaires_configurer_facteur_charger_dist() {
	include_spip('inc/config');
	include_spip('classes/facteur');

	$valeurs = array(
		'facteur_adresse_envoi'             => lire_config('facteur_adresse_envoi'),
		'facteur_adresse_envoi_nom'         => lire_config('facteur_adresse_envoi_nom'),
		'facteur_adresse_envoi_email'       => lire_config('facteur_adresse_envoi_email'),
		'facteur_forcer_from'               => lire_config('facteur_forcer_from'),
		'facteur_mailer'                    => facteur_config_mailer(),
		'facteur_smtp_host'                 => lire_config('facteur_smtp_host'),
		'facteur_smtp_port'                 => lire_config('facteur_smtp_port', 25),
		'facteur_smtp_auth'                 => lire_config('facteur_smtp_auth'),
		'facteur_smtp_username'             => lire_config('facteur_smtp_username'),
		'facteur_smtp_password'             => '',
		'_facteur_smtp_password'            => lire_config('facteur_smtp_password'),
		'facteur_smtp_secure'               => lire_config('facteur_smtp_secure'),
		'facteur_smtp_tls_allow_self_signed'=> lire_config('facteur_smtp_tls_allow_self_signed'),
		'facteur_smtp_sender'               => lire_config('facteur_smtp_sender'),
		'facteur_filtre_images'             => lire_config('facteur_filtre_images'),
		'facteur_filtre_iso_8859'           => lire_config('facteur_filtre_iso_8859'),
		'facteur_cc'                        => lire_config('facteur_cc'),
		'facteur_bcc'                       => lire_config('facteur_bcc'),
		'email_test'                        => lire_config('facteur_adresse_envoi') == 'oui' ? lire_config('facteur_adresse_envoi_email') : $GLOBALS['meta']['email_webmaster'],
		'tester'                            => '',
	);

	// recuperer le from par defaut actuel pour l'indiquer dans le formulaire
	$facteur = new Facteur('test@example.org', 'Test', '', '', array('adresse_envoi' => 'non'));
	$valeurs['_from_defaut'] = $facteur->From;
	if ($facteur->FromName) {
		$valeurs['_from_defaut'] = $facteur->FromName . ' &lt;'.$valeurs['_from_defaut'].'&gt;';
	}

	return $valeurs;
}

function formulaires_configurer_facteur_verifier_dist() {
	$erreurs = array();
	include_spip('inc/config');
	if ($email = _request('facteur_adresse_envoi_email')
		and !email_valide($email)) {
		$erreurs['facteur_adresse_envoi_email'] = _T('form_email_non_valide');
		set_request('facteur_adresse_envoi', 'oui');
	}
	if (_request('facteur_mailer') === 'smtp') {
		if (!($h = _request('facteur_smtp_host'))) {
			$erreurs['facteur_smtp_host'] = _T('info_obligatoire');
		} else {
			$h = trim($h);
			$regexp_ip_valide = '#^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))|((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$#';
			// Source : http://www.d-sites.com/2008/10/09/regex-ipv4-et-ipv6/
			if (!preg_match($regexp_ip_valide, $h)) { // ce n'est pas une IP
				if (!preg_match(';^([^.\s/?:]+[.])*[^.\s/?:]+$;', $h)
					or gethostbyname($h) == $h) {
					$erreurs['facteur_smtp_host'] = _T('facteur:erreur_invalid_host');
				}
			} else {
				if (gethostbyaddr($h) == $h) {
					$erreurs['facteur_smtp_host'] = _T('facteur:erreur_invalid_host');
				}
			}
			set_request('facteur_smtp_host', $h);
		}
		if (!($p=_request('facteur_smtp_port'))) {
			$erreurs['facteur_smtp_port'] = _T('info_obligatoire');
		} elseif (!preg_match(';^[0-9]+$;', $p) or !intval($p)) {
			$erreurs['facteur_smtp_port'] = _T('facteur:erreur_invalid_port');
		}

		if (!_request('facteur_smtp_auth')) {
			$erreurs['facteur_smtp_auth'] = _T('info_obligatoire');
		}

		if (_request('facteur_smtp_auth')=='oui') {
			if (!_request('facteur_smtp_username')) {
				$erreurs['facteur_smtp_username'] = _T('info_obligatoire');
			}
			if (!_request('facteur_smtp_password') and !lire_config('facteur_smtp_password')) {
				$erreurs['facteur_smtp_password'] = _T('info_obligatoire');
			}
		}
	}
	if ($emailcc = _request('facteur_cc')
		and !email_valide($emailcc)) {
		$erreurs['facteur_cc'] = _T('form_email_non_valide');
	}
	if ($emailbcc = _request('facteur_bcc')
		and !email_valide($emailbcc)) {
		$erreurs['facteur_bcc'] = _T('form_email_non_valide');
	}

	if (_request('tester')) {
		if (!$email = _request('email_test')) {
			$erreurs['email_test'] = _T('info_obligatoire');
		} elseif (!email_valide($email)) {
			$erreurs['email_test'] = _T('form_email_non_valide');
		}
	}


	if (count($erreurs) > 0) {
		$erreurs['message_erreur'] = _T('facteur:erreur_generale');
	}
	return $erreurs;
}

function formulaires_configurer_facteur_traiter_dist() {
	include_spip('inc/meta');

	$facteur_adresse_envoi = _request('facteur_adresse_envoi');
	ecrire_meta('facteur_adresse_envoi', ($facteur_adresse_envoi=='oui')?'oui':'non');

	$facteur_adresse_envoi_nom = _request('facteur_adresse_envoi_nom');
	ecrire_meta('facteur_adresse_envoi_nom', $facteur_adresse_envoi_nom?$facteur_adresse_envoi_nom:'');

	$facteur_adresse_envoi_email = _request('facteur_adresse_envoi_email');
	ecrire_meta('facteur_adresse_envoi_email', $facteur_adresse_envoi_email?$facteur_adresse_envoi_email:'');

	$facteur_forcer_from = _request('facteur_forcer_from');
	ecrire_meta('facteur_forcer_from', ($facteur_forcer_from=='oui')?'oui':'non');

	$facteur_mailer = _request('facteur_mailer');
	ecrire_meta('facteur_mailer', $facteur_mailer);

	$facteur_smtp_host = _request('facteur_smtp_host');
	ecrire_meta('facteur_smtp_host', $facteur_smtp_host?$facteur_smtp_host:'');

	$facteur_smtp_port = _request('facteur_smtp_port');
	ecrire_meta('facteur_smtp_port', strlen($facteur_smtp_port)?intval($facteur_smtp_port):'');

	$facteur_smtp_auth = _request('facteur_smtp_auth');
	ecrire_meta('facteur_smtp_auth', ($facteur_smtp_auth=='oui')?'oui':'non');

	$facteur_smtp_username = _request('facteur_smtp_username');
	ecrire_meta('facteur_smtp_username', $facteur_smtp_username);

	if ($facteur_smtp_password = _request('facteur_smtp_password')) {
		ecrire_meta('facteur_smtp_password', $facteur_smtp_password);
	}

	$facteur_smtp_secure = _request('facteur_smtp_secure');
	ecrire_meta('facteur_smtp_secure', in_array($facteur_smtp_secure, array('non', 'ssl', 'tls')) ? $facteur_smtp_secure : 'non');

	$facteur_smtp_tls_allow_self_signed = _request('facteur_smtp_tls_allow_self_signed');
	ecrire_meta('facteur_smtp_tls_allow_self_signed', ($facteur_smtp_tls_allow_self_signed=='oui')?'oui':'non');

	$facteur_smtp_sender = _request('facteur_smtp_sender');
	ecrire_meta('facteur_smtp_sender', $facteur_smtp_sender);

	ecrire_meta('facteur_filtre_images', intval(_request('facteur_filtre_images')));
	ecrire_meta('facteur_filtre_iso_8859', intval(_request('facteur_filtre_iso_8859')));

	$facteur_cc = _request('facteur_cc');
	ecrire_meta('facteur_cc', $facteur_cc?$facteur_cc:'');

	$facteur_bcc = _request('facteur_bcc');
	ecrire_meta('facteur_bcc', $facteur_bcc?$facteur_bcc:'');


	$res = array('message_ok' => _T('facteur:config_info_enregistree'));

	return $res;
}
