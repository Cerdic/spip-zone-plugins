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
		'session_email' => ''
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

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_newsletter_subscribe_traiter_dist($listes = '', $option = '') {

	// langue par defaut lors de l'inscription : la langue courante dans la page
	$options = array('lang' => $GLOBALS['spip_lang']);
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

	$newsletter_subscribe = charger_fonction("subscribe", "newsletter");
	if ($newsletter_subscribe($email, $options)) {
		if (lire_config('mailsubscribers/double_optin', 0)) {
			$res['message_ok'] = _T('newsletter:subscribe_message_ok_confirm', array('email' => "<b>$email</b>"));
		} else {
			$res['message_ok'] = _T('newsletter:subscribe_message_ok', array('email' => "<b>$email</b>"));
		}
	} else {
		$res['message_erreur'] = _T('mailsubscriber:erreur_technique_subscribe');
	}
	set_request('session_email');

	return $res;
}

