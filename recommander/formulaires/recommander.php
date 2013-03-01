<?php
/*
 * Plugin Recommander a un ami
 * (c) 2006-2010 Fil
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

/**
 * Charger les valeurs du formulaire recommander
 * @param string $titre
 * @param string $url
 * @param string $texte
 * @param string $subject
 * @return array
 */
function formulaires_recommander_charger_dist($titre, $url='', $texte='', $subject=''){
	$valeurs = array(
		'recommander_from'=> $GLOBALS['visiteur_session']['email'] ? $GLOBALS['visiteur_session']['email'] :'',
		'recommander_to'=> '',
		'recommander_message'=> ''
	);

	return $valeurs;
}

/**
 * Verifier les valeurs du formulaire recommander
 * @param string $titre
 * @param string $url
 * @param string $texte
 * @param string $subject
 * @return array
 */
function formulaires_recommander_verifier_dist($titre, $url='', $texte='', $subject=''){
	$erreurs = array();

	foreach(array('recommander_from','recommander_to') as $c) {
		if (!$email = trim(_request($c)))
			$erreurs[$c] = _T('form_prop_indiquer_email');
		elseif (!email_valide($email))
			$erreurs[$c] = _T('pass_erreur_non_valide', array(
				'email_oubli' => htmlspecialchars($email)
				)
			);
	}

	return $erreurs;
}


/**
 * Envoyer le mail
 * @param string $titre
 * @param string $url
 * @param string $texte
 * @param string $subject
 * @return array
 */
function formulaires_recommander_traiter_dist($titre, $url='', $texte='', $subject=''){

	$subject = sinon ($subject,
		_T('recommander:recommander_titre',array('nom_site'=>$GLOBALS['meta']['nom_site']))
		.sinon($titre, _request('recommander_titre'))
	);

	$contexte = array(
		'titre'=>$titre,
		'texte'=>$texte,
		'url'=>$url ? $url : self(),
		'recommander_from'=>_request('recommander_from'),
		'recommander_to'=>_request('recommander_to'),
		'recommander_message'=>_request('recommander_message'),
	);
	$body = recuperer_fond('modeles/recommander_email',$contexte);
	$header = "X-Originating-IP: ".$GLOBALS['ip']."\n";

	$res = true;
	if (
		include_spip("inc/notifications")
		AND function_exists('notifications_envoyer_mails')){
		notifications_envoyer_mails(_request('recommander_to'), $body, $subject, _request('recommander_from'), $header);
	}
	else {
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		if (!$envoyer_mail(_request('recommander_to'),$subject,$body,_request('recommander_from'),$header))
			$res = false;
	}
	if (!$res)
		return array('message_erreur' => _L("Erreur lors de l'envoi du message."));
	else
		return array('message_ok' => recuperer_fond('modeles/recommander_envoye',$contexte));
}

?>