<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_tester_newsletter_charger_dist($id_newsletter){

	$valeurs = array(
		'email_test' => $GLOBALS['visiteur_session']['email'],
	);

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_tester_newsletter_verifier_dist($id_newsletter){

	$erreurs = array();

	if (!_request('email_test'))
		$erreurs['email_test'] = _T('info_obligatoire');

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_tester_newsletter_traiter_dist($id_newsletter){

	$email = _request('email_test');

	// recuperer l'abonne si il existe avec cet email
	$subscriber = charger_fonction('subscriber','newsletter');
	$dest = $subscriber($email);

	// si abonne inconnu, on simule (pour les tests)
	if (!$dest)
		$dest = array(
			'email' => $email,
			'nom' => $GLOBALS['visiteur_session']['nom'],
			'lang' => $GLOBALS['visiteur_session']['lang'],
			'status' => 'on',
			'url_unsubscribe' => url_absolue(_DIR_RACINE . "unsubscribe"),
		);

	// generer une version a jour (ne fera rien si deja cuite)
	$generer_newsletter = charger_fonction("generer_newsletter","action");
	$generer_newsletter($id_newsletter);

	// fixer les images et autre
	$fixer_newsletter = charger_fonction("fixer_newsletter","action");
	$fixer_newsletter($id_newsletter);

	// ok, maintenant on prepare un envoi
	$send = charger_fonction("send","newsletter");
	$res = $send($dest, $id_newsletter, array('test'=>true));

	if (!$res)
		$res = array('message_ok'=>_T('newsletter:info_test_envoye',array('email'=>$email)));
	else
		$res = array('message_erreur'=>$res);

	return $res;
}


?>