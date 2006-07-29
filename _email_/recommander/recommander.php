<?php

// Envoyer cet article par email
//
// On l'ajoute dans n'importe quel squelette en precisant
// comme parametre d'inclusion :
// - le titre de la page
// - l'url de la page
// - le texte a afficher
//
// <INCLURE(recommander.php) {titre=#TITRE} {url=#URL_ARTICLE}
//   {texte=#INTRODUCTION} {lang}>
//
// On peut ajouter {subject=xxxx} si on veut fixer le sujet,
// sinon par defaut c'est "A lire sur #NOM_SITE -- #ENV{titre}"

// TODO :
// - internationalisation
// - presentation (squelette ?)
// - previsualisation et explications (on va envoyer ce message de votre part)

/*
>È A lire sur XXXXXX                                                            
Ler em XXXXX
>                                                                               
>                                                                               
>È Message envoyŽ !                                                             
Mensagem enviada!
>                                                                               
>È destinataire :                                                               
Destinat‡rio:
>                                                                               
>                                                                               
>È XXXX vous recommande la lecture de cet article :                             
XXXXXX recomenda a leitura deste texto
>                                                                               
>                                                                               
>È Erreur lors de l'envoi du message.                                           
Erro no envio da mensagem
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

define ('_SECRET', '1234');  # trouver une meilleure methode pour definir le secret... un meta() dans la base...

//
// Fonction appelee des qu'il y a un $_POST avec le bouton 'recommander'
//
function recommander($contexte_inclus) {
	include_spip('inc/filtres');
	$retour = '';

	lang_select($contexte_inclus['lang']);

	// verifier que le formulaire est bien rempli
	if (!email_valide(_request('recommander_from')))
		$retour = _T('pass_erreur_non_valide',
			array(
			'email_oubli' => htmlspecialchars(_request('recommander_from'))
			)
		);

	if (!email_valide(_request('recommander_to')))
		$retour = _T('pass_erreur_non_valide',
			array(
			'email_oubli' => htmlspecialchars(_request('recommander_to'))
			)
		);

	if ($retour)
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

	if (!envoyer_mail(
		_request('recommander_to'),
		$subject,
		$body,
		_request('recommander_from'),
		"X-Originating-IP: ".$GLOBALS['ip']
	))
		return "Erreur lors de l'envoi du message.";

}


//
// Fonction de base de cet INCLURE
//
// Dans la variable $contexte_inclus on trouve les donnees de l'article
// dans _request() les donnees du visiteur
function main_recommander() {
	$erreur ='';
	include_spip('inc/lang');
	lang_select($GLOBALS['contexte_inclus']['lang']);
	if (!_request('recommander_env')
	OR (_request('recommander_cle') <> md5(_SECRET._request('recommander_env')))
	OR $erreur = recommander(@unserialize(base64_decode(_request('recommander_env'))))) {

		$r = $erreur;

		// le formulaire normal
		$r .= "<form method='post' action='".self()."'
		onsubmit=\"ahahform('spip.php', 'recommander');return false;\">";
		$r .= _T('form_pet_votre_email')." <input type='text' name='recommander_from'
		value='".htmlspecialchars(_request('recommander_from'))."' />";
		$r .= "<br />"._L('destinataire :')." <input type='text' name='recommander_to'
		value='".htmlspecialchars(_request('recommander_to'))."' />";
		$r .= "<br />"._T('forum_texte')." <input type='text' name='recommander_message'
		value='".htmlspecialchars(_request('recommander_message'))."' />";
		$r .= "<input type='submit' name='recommander_email' value='"._T('recommander_message')."' />";

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

	} else {
		$r .= _T('form_prop_message_envoye');
	}
	lang_dselect();

	return $r;
}

// main()
echo "<script type='text/javascript' src='squelettes/ahah.js'></script>\n",
	"<div id='recommander' class='formulaire_spip'>\n",
	"<span></span>\n", # pour l'icone "searching..."
	main_recommander(),
	"</div>\n";

?>
