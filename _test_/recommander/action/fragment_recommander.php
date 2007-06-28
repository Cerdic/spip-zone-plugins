<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define ('_SECRET', '1234');  # trouver une meilleure methode pour definir le secret... un meta() dans la base...



function verifier_email_ou_erreur($email) {
	if (!$email = trim($email))
		return '<div class="erreur">'._T('form_prop_indiquer_email').'</div>';
	if (!email_valide($email))
		return '<div class="erreur">'
			. _T('pass_erreur_non_valide',
				array(
				'email_oubli' => htmlspecialchars($email)
				)
			).'</div>';
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
	include_spip('inc/mail');
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
	$body = "Bonjour,\n\n"
		. _request('recommander_from')
		. " vous recommande la lecture de cet article :\n\n"
		. "* ". textebrut($contexte_inclus['titre'])." *\n\n"
		. textebrut($contexte_inclus['texte'])."\n\n"
		. ' -> '.url_absolue(sinon ($contexte_inclus['url'], self()))
		. "\n\n"
		. _request('recommander_message')
		. "\n\n-- "._T('envoi_via_le_site')
		. " ".supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']))
		. " (".$GLOBALS['meta']['adresse_site']."/) --\n";

	lang_dselect();

	if (!envoyer_mail(
		_request('recommander_to'),
		$subject,
		$body,
		_request('recommander_from'),
		"X-Originating-IP: ".$GLOBALS['ip']
	))
		return "<div class='erreur'>"._L("Erreur lors de l'envoi du message.")."</div>";

}


function action_fragment_recommander() {
	if (!_request('recommander_env')
	OR (_request('recommander_cle') <> md5(_SECRET._request('recommander_env')))
	OR $erreur = envoi_recommander(
		@unserialize(base64_decode(_request('recommander_env'))))
	) {

		$r = $erreur;

		$r .= "<form method='post' action='".self()."'>";

		$r .= "<p><label for='recommander_from'>"._T('form_pet_votre_email')."</label>";
		$r .= " <input type='text' id='recommander_from' name='recommander_from'
		value='".htmlspecialchars(_request('recommander_from'))."'  class='forml' /></p>";
		$r .= "<p><label for='recommander_to'>"._T('recommander:destinataire')."</label>";
		$r .= " <input type='text' id='recommander_to' name='recommander_to' class='forml'
		value='".htmlspecialchars(_request('recommander_to'))."' class='formo' /></p>";
		$r .= "<p><label for='recommander_message'>"._T('forum_texte')."</label>";
		$r .= " <textarea id='recommander_message' name='recommander_message' class='forml'
		value='".htmlspecialchars(_request('recommander_message'))."' class='forml'></textarea></p>";
		$r .= "<div class='spip_bouton'><input type='submit' name='recommander_email' value='"._T('recommander:recommander_message')."' /></div>";

		if (!_request('recommander_cle')) {
			$contexte = base64_encode(serialize($GLOBALS['contexte_inclus']));
			$cle = md5(_SECRET.$contexte);
		} else {
			$contexte = htmlspecialchars(_request('recommander_env'));
			$cle = htmlspecialchars(_request('recommander_cle'));
		}
		$r .= "<input type='hidden' name='recommander_env' value='$contexte' />\n";
		$r .= "<input type='hidden' name='recommander_cle' value='$cle' />\n";
		$r .= "</form>";
	}
	
	else {
		$r = _T('form_prop_message_envoye');
	}

	echo $r;
}

?>
