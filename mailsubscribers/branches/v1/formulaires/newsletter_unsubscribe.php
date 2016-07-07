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
function formulaires_newsletter_unsubscribe_charger_dist($listes=''){
	$valeurs = array(
		'email' => ''
	);


	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_newsletter_unsubscribe_verifier_dist($listes=''){

	$erreurs = array();
	if (!$email = _request('email_unsubscribe')){
		$erreurs['email_unsubscribe'] = _T('info_obligatoire');
	}
	else {
		// verifier que l'email est valide
		if (!email_valide($email))
			$erreurs['email_unsubscribe'] = _T('info_email_invalide');
	}
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_newsletter_unsubscribe_traiter_dist($listes=''){

	// langue par defaut lors de l'inscription : la langue courante dans la page
	$email = _request('email_unsubscribe');
	$options = array();
	if ($listes AND is_string($listes))
		$listes = explode(',',$listes);
	if ($listes AND is_array($listes) AND count($listes))
		$options['listes'] = $listes;

	$newsletter_unsubscribe = charger_fonction("unsubscribe","newsletter");
	$newsletter_unsubscribe($email, $options);

	set_request('email_unsubscribe');
	return array('message_ok'=>_T('newsletter:unsubscribe_message_ok',array('email'=>"<b>$email</b>")),'editable'=>true);
}


?>
