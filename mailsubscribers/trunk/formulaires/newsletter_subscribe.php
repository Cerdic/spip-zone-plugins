<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 * @param string|array $listes
 * @param string $option
 *   '', 'checklist', 'list'
 * @return array|bool
 */
function formulaires_newsletter_subscribe_charger_dist($listes = '', $option = '') {
	if ($listes AND is_string($listes)) {
		$listes = explode(',', $listes);
	}

	$valeurs = array(
		'session_email' => '',
		'_nospam_encrypt' => 'all'
	);

	if (isset($GLOBALS['visiteur_session']['email'])) {
		$valeurs['session_email'] = $GLOBALS['visiteur_session']['email'];
	} elseif (isset($GLOBALS['visiteur_session']['session_email'])) {
		$valeurs['session_email'] = $GLOBALS['visiteur_session']['session_email'];
	}

	if ($listes){
		if ($option==='checklist') {
			$valeurs['_listes_choix'] = $listes;
			$valeurs['_checkable'] = ' ';
			$valeurs['listes'] = $listes;
		}
		elseif($option==='list') {
			$valeurs['_listes_choix'] = $listes;
		}
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_newsletter_subscribe_verifier_dist($listes = '', $option = '') {

	$erreurs = array();

	if (include_spip('inc/nospam_encrypt') and function_exists('nospam_encrypt_decrypt_post')) {
		set_request('session_email'); // on ne veut pas d'un POST direct sous ce nom car trop facile et trop de spam
		$res = nospam_encrypt_decrypt_post('newsletter_subscribe');
		if (is_string($res)) {
			$erreurs['message_erreur'] = $res;
		}
	}

	if (!isset($erreurs['message_erreur'])) {
		if ($listes AND is_string($listes)) {
			$listes = explode(',', $listes);
		}

		if ($listes and $option==='checklist' and !_request('listes')){
			$erreurs['listes'] = _T('info_obligatoire');
			set_request('listes',array());
		}

		if (!$email = _request('session_email')) {
			$erreurs['session_email'] = _T('info_obligatoire');
		} else {
			// verifier que l'email est valide
			if (!email_valide($email)) {
				$erreurs['session_email'] = _T('info_email_invalide');
			}
		}
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_newsletter_subscribe_traiter_dist($listes = '', $option = '') {

	$options = array(
		// langue par defaut lors de l'inscription : la langue courante dans la page
		'lang' => $GLOBALS['spip_lang'],
		// on est pas graceful ici puisqu'a priori c'est un canal de reinscription, y compris si on s'etait desinscrit auparavant
		'graceful' => false,
	);
	$email = _request('session_email');
	if ($listes AND is_string($listes)) {
		$listes = explode(',', $listes);
	}
	if ($listes AND is_array($listes) AND count($listes)) {
		if ($option==='checklist'){
			$listes = array_intersect($listes, _request('listes'));
		}
		include_spip('inc/mailsubscribers');
		$listes_dispo = mailsubscribers_listes(array('status'=>'open'));
		$listes = array_intersect($listes, array_keys($listes_dispo));
		$options['listes'] = $listes;
	}

	$res = array(
		'editable' => true
	);

	if (lire_config('mailsubscribers/double_optin', 0)) {
		$res['message_ok'] = _T('newsletter:subscribe_message_ok_confirm', array('email' => "<b>$email</b>"));
	} else {
		$res['message_ok'] = _T('newsletter:subscribe_message_ok', array('email' => "<b>$email</b>"));
	}

	include_spip('inc/nospam');
	if (function_exists('nospam_confirm_action_html')) {
		$html_confirm = nospam_confirm_action_html("subscribe", "Subscribe $email " . json_encode($options), array($email, $options), "newsletter/");
		$res['message_ok'] .= $html_confirm;
	}
	else {
		$newsletter_subscribe = charger_fonction("subscribe", "newsletter");
		if (!$newsletter_subscribe($email, $options)) {
			unset($res['message_ok']);
			$res['message_erreur'] = _T('mailsubscriber:erreur_technique_subscribe');
		}
	}
	set_request('session_email');

	return $res;
}

