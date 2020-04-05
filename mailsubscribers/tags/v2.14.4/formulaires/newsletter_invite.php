<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_newsletter_invite_charger_dist($listes = '') {
	$valeurs = array(
		'to_email' => '',
		'from_email' => '',
		'afficher_from_email' => 'oui',
		'message_invite_email_subscribe' => ''
	);

	if (isset($GLOBALS['visiteur_session']['email'])) {
		$valeurs['from_email'] = $GLOBALS['visiteur_session']['email'];
	} elseif (isset($GLOBALS['visiteur_session']['session_email'])) {
		$valeurs['from_email'] = $GLOBALS['visiteur_session']['session_email'];
	}

	if ($valeurs['from_email'] != '') {
		$valeurs['afficher_from_email'] = 'non';
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_newsletter_invite_verifier_dist($listes = '') {
	$erreurs = array();

	if (!isset($GLOBALS['visiteur_session']['email'])
		AND isset($GLOBALS['visiteur_session']['session_email'])
	) {

		if (!$email = _request('from_email')) {
			$erreurs['from_email'] = _T('info_obligatoire');
		} else {
			// verifier que l'email est valide
			if (!email_valide($email)) {
				$erreurs['from_email'] = _T('info_email_invalide');
			}
		}
	}

	// Email destinataires
	if (!$to_email = _request('to_email')) {
		$erreurs['to_email'] = _T('mailsubscriber:info_email_obligatoire');
	} else {
		$lesemails = preg_split("/,/", $to_email);
		// Un seul email
		if (count($lesemails) == 1) {
			// verifier que l'email est valide
			if (!email_valide($lesemails[0])) {
				$erreurs['to_email'] = _T('info_email_invalide');
			}
		} else {
			if (count($lesemails) > 5) {
				$erreurs['to_email'] .= _T('mailsubscriber:info_email_limite_nombre');

			} else {
				$emailinvalide = 0;
				foreach ($lesemails as $unemail) {
					if (!email_valide($unemail)) {
						$emailinvalide++;
						$erreurs['to_email'] .= _T('info_email_invalide') . " $unemail<br/>";
					}
				}
			}
		}

	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_newsletter_invite_traiter_dist($listes = '') {

	// langue par defaut lors de l'inscription : la langue courante dans la page
	$options = array('lang' => $GLOBALS['spip_lang']);

	if (isset($GLOBALS['visiteur_session']['email'])) {
		$from_email = $GLOBALS['visiteur_session']['email'];
	} elseif (isset($GLOBALS['visiteur_session']['session_email'])) {
		$from_email = $GLOBALS['visiteur_session']['session_email'];
	} else {
		$from_email = _request('from_email');
	}

	$options['invite_email_from'] = $from_email;
	$options['invite_email_text'] = _request('message_invite_email_subscribe');

	// pour une invitation on force le double optin
	$options['force'] = -1;


	if ($listes AND is_string($listes)) {
		$listes = explode(',', $listes);
	}
	if ($listes AND is_array($listes) AND count($listes)) {
		$options['listes'] = $listes;
	}

	$res = array(
		'editable' => true
	);
	$newsletter_subscribe = charger_fonction("subscribe", "newsletter");

	$resultatko = 0;
	$resultatko_emails = "";
	$resultatok_emails = "";
	$lesemails = preg_split("/,/", _request('to_email'));
	foreach ($lesemails as $unemail) {
		if (!$newsletter_subscribe($unemail, $options)) {
			$resultatko += 1;
			$resultatko_emails .= $unemail . ",";
		} else {
			$resultatok_emails .= $unemail . ",";
		}
	}

	if ($resultatko == 0) {
		if (count($lesemails) > 1) {
			$res['message_ok'] = _T('newsletter:subscribe_message_ok_confirm_invite_pluriel',
				array('email' => "<b>$resultatok_emails</b>"));
		} else {
			$res['message_ok'] = _T('newsletter:subscribe_message_ok_confirm_invite_singulier',
				array('email' => "<b>$resultatok_emails</b>"));
		}
	} else {
		$res['message_erreur'] = _T('mailsubscriber:erreur_technique_subscribe');
	}
	set_request('email');

	return $res;
}
