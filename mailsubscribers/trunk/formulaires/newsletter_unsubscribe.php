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
function formulaires_newsletter_unsubscribe_charger_dist($listes = '', $option = '') {
	if ($listes AND is_string($listes)) {
		$listes = explode(',', $listes);
	}

	$valeurs = array(
		'email' => ''
	);

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
function formulaires_newsletter_unsubscribe_verifier_dist($listes = '', $option = '') {

	$erreurs = array();

	if ($listes and $option==='checklist' and !_request('listes')){
		$erreurs['listes'] = _T('info_obligatoire');
		set_request('listes',array());
	}

	if (!$email = _request('email_unsubscribe')) {
		$erreurs['email_unsubscribe'] = _T('info_obligatoire');
	} else {
		// verifier que l'email est valide
		if (!email_valide($email)) {
			$erreurs['email_unsubscribe'] = _T('info_email_invalide');
		}
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_newsletter_unsubscribe_traiter_dist($listes = '', $option = '') {

	// langue par defaut lors de l'inscription : la langue courante dans la page
	$email = _request('email_unsubscribe');
	$options = array();
	if ($listes AND is_string($listes)) {
		$listes = explode(',', $listes);
	}
	if ($listes AND is_array($listes) AND count($listes)) {
		if ($option==='checklist'){
			$listes = array_intersect($listes, _request('listes'));
		}
		$options['listes'] = $listes;
	}

	$newsletter_unsubscribe = charger_fonction("unsubscribe", "newsletter");
	$newsletter_unsubscribe($email, $options);

	set_request('email');

	return array(
		'message_ok' => _T('newsletter:unsubscribe_message_ok', array('email' => "<b>$email</b>")),
		'editable' => true
	);
}
