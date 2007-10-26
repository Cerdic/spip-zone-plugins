<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// initaliser le secret la premiere fois : md5 de l'url et de la date
if (!isset($GLOBALS['meta']['recommander_secret'])){
	include_spip('inc/meta');
	ecrire_meta('recommander_secret',md5(self().date('Y-m-d H:i:s')));
	ecrire_metas();
}

function verifier_email_ou_erreur($email) {
	if (!$email = trim($email))
		return _T('form_prop_indiquer_email').'<br />';
	if (!email_valide($email))
		return _T('pass_erreur_non_valide',
				array(
				'email_oubli' => htmlspecialchars($email)
				)
			).'<br />';
}

//
// Fonction appelee des qu'il y a un $_POST avec le bouton 'recommander'
//
function envoi_recommander($contexte_inclus) {
	include_spip('inc/filtres');
	$retour = '';

	lang_select($contexte_inclus['lang']);

	// verifier que le formulaire est bien rempli
	if ($retour = verifier_email_ou_erreur(_request('recommander_from'))
	. verifier_email_ou_erreur(_request('recommander_to')))
		return $retour;

	// envoyer le mail
	include_spip('inc/filtres');
#	var_dump($contexte_inclus);

# i18n
# _T('recommander_titre', array('nom_site' => 
# supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site'])))
	$subject = sinon ($contexte_inclus['subject'],
		_L("A lire sur ").lire_meta('nom_site')." -- "
		.sinon($contexte_inclus['titre'], _request('recommander_titre'))
	);

# i18n
# _T('recommander_lecture', array('from' => _request('recommander_from')))
	$contexte = array(
		'titre'=>$contexte_inclus['titre'],
		'texte'=>$contexte_inclus['texte'],
		'url'=>$contexte_inclus['url']?$contexte_inclus['url']:self(),
		'recommander_from'=>_request('recommander_from'),
		'recommander_to'=>_request('recommander_to'),
		'recommander_message'=>_request('recommander_message'),
	);
	include_spip('public/assembler');
	$body = recuperer_fond('recommander/email',$contexte);

	lang_dselect();

	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	if (!$envoyer_mail(
		_request('recommander_to'),
		$subject,
		$body,
		_request('recommander_from'),
		"X-Originating-IP: ".$GLOBALS['ip']
	))
		return _L("Erreur lors de l'envoi du message.");

}


function action_fragment_recommander($return = false, $contexte_inclus = array()) {
	if (!_request('recommander_env')
	OR (_request('recommander_cle') <> md5($GLOBALS['meta']['recommander_secret']._request('recommander_env')))
	OR $erreur = envoi_recommander(
		@unserialize(base64_decode(_request('recommander_env'))))
	) {
		if (!_request('recommander_cle')) {
			$contexte = base64_encode(serialize($contexte_inclus));
			$cle = md5($GLOBALS['meta']['recommander_secret'].$contexte);
		} else {
			$contexte = htmlspecialchars(_request('recommander_env'));
			$cle = htmlspecialchars(_request('recommander_cle'));
		}
		
		$contexte = array(
			'erreur' =>$erreur,
			'recommander_env'=>$contexte,
			'recommander_cle'=>$cle,
			'recommander_from'=>_request('recommander_from'),
			'recommander_to'=>_request('recommander_to'),
			'recommander_message'=>_request('recommander_message'),
			'titre'=>$contexte_inclus['titre'],
			'texte'=>$contexte_inclus['texte'],
			'url'=>$contexte_inclus['url'],
			'self'=>self()
		);
		include_spip('public/assembler');
		$r = recuperer_fond('recommander/formulaire',$contexte);
	}
	
	else {
		$contexte = array(
			'recommander_from'=>_request('recommander_from'),
			'recommander_to'=>_request('recommander_to'),
			'recommander_message'=>_request('recommander_message'),
			'titre'=>$contexte_inclus['titre'],
			'texte'=>$contexte_inclus['texte'],
			'url'=>$contexte_inclus['url'],
		);
		include_spip('public/assembler');
		$r = recuperer_fond('recommander/envoye',$contexte);
	}

	if ($return)
		return $r;
	else 
		echo $r;
}

?>
