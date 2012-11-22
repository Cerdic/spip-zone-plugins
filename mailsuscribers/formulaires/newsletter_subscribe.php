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
function formulaires_newsletter_subscribe_charger_dist(){
	$valeurs = array(
		'session_email' => ''
	);

	if (isset($GLOBALS['visiteur_session']['email']))
		$valeurs['session_email'] = $GLOBALS['visiteur_session']['email'];
	elseif (isset($GLOBALS['visiteur_session']['session_email']))
		$valeurs['session_email'] = $GLOBALS['visiteur_session']['session_email'];

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_newsletter_subscribe_verifier_dist(){

	$erreurs = array();
	if (!$email = _request('session_email')){
		$erreurs['session_email'] = _T('info_obligatoire');
	}
	else {
		// verifier que l'email est valide
		if (!email_valide($email))
			$erreurs['session_email'] = _T('info_email_invalide');
	}
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_newsletter_subscribe_traiter_dist(){

	// langue par defaut lors de l'inscription : la langue courante dans la page
	$lang = $GLOBALS['spip_lang'];
	$email = _request('session_email');

	$newsletter_subscribe = charger_fonction("subscribe","newsletter");
	$newsletter_subscribe($email,array('lang'=>$lang));

	set_request('email');
	return array('message_ok'=>_T('newsletter:subscribe_message_ok',array('email'=>$email)),'editable'=>true);
}


?>