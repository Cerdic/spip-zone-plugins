<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite
include_spip ("inc/texte");

function balise_FORMULAIRE_PROPAGANDA ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_PROPAGANDA', array('id_article'));
}

function balise_FORMULAIRE_PROPAGANDA_stat($args, $filtres) {
	// Pas d'id_auteur ni d'id_article ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_PROPAGANDA',
					'motif' => 'ARTICLES')), '');

	return $args;
}

function balise_FORMULAIRE_PROPAGANDA_dyn($id_article) {
include_spip('inc/mail');

	global $REMOTE_ADDR, $afficher_texte, $_COOKIE, $_POST;

	$destinataire = _request('email_destinataire');
	$sujet = _request('sujet_message_auteur');
	$adres = _request('email_message_auteur');
	$texte = _request('texte_message_auteur');
	$auteur =_request('auteur');
	$nom_expediteur = _request('nom_expediteur');
	$url_carte = _request('url_carte');

	$previsualiser= _request('previsualiser');
	$valider= _request('valider');
	
	$previsu = '';
	$bouton= '';

	// doit-on envoyer le mail ?
	if ($valider)
{
		$texte2 = ""._T('bonjour')."\n\n$nom_expediteur ($adres) "._T('pense_a_vous_carte')." "._T('avec_message')."\n\n";
		$texte2 .= $texte;
		$texte2 .= "\n\n"._T('consulter_carte')." \n"._T('adresse_carte')."$url_carte\n\n"._T('soin_de_vous')."\n\nSklunk.net";
		$texte2 .= "\n\n-- "._T('envoi_via_le_site')." ".supprimer_tags(extraire_multi(lire_meta('nom_site')))." (".lire_meta('adresse_site')."/) --\n";
		$sujet2 = "[SKLUNK.NET] - ";
		$sujet2 .= $sujet;
		$sujet2 = utf8_decode($sujet2);
		envoyer_mail($destinataire, $sujet2, $texte2, $adres,
				"X-Originating-IP: ".$GLOBALS['REMOTE_ADDR']);
		return _T('form_prop_message_envoye');
	}
	
	else{
		if($previsualiser)
		{

		if (!$adres){$erreur .= _T('form_indiquer_email');}
		else if (!$nom_expediteur){$erreur .= _T('form_indiquer_nom');}
		else if (!$destinataire){$erreur .= _T('form_indiquer_destinataire');}
		else if (strlen($sujet) < 3){$erreur .= _T('forum_attention_trois_caracteres');}
		else if (!email_valide($adres)){$erreur .= _T('info_email_invalide');}
		else if (!$url_carte){$erreur .= _T('form_carte_invalide');}
		if(!$erreur){$bouton= _T('form_prop_confirmer_envoi');}


		$previsu = inclure_balise_dynamique(
			array(
				'formulaire_propaganda_previsu',
				0,
			array(
				'mail' => $adres,
				'nom_expediteur' => $nom_expediteur,
				'destinataire' => $destinataire,
				'sujet' => $sujet,
				'texte' => $texte,
				'url_carte' => $url_carte,
				'erreur' => $erreur,
				'bouton' => $bouton,
				)
			),
			false);
				$previsu = preg_replace("@<(/?)f(orm[>[:space:]])@ism",
				"<\\1no-f\\2", $previsu);
		}
	return 
		array('formulaire_propaganda', 0,
			array(
			'mail' => $adres,
			'nom_expediteur' => $nom_expediteur,
			'previsu' => $previsu,
			'destinataire' => $destinataire,
			'sujet' => $sujet,
			'texte' => $texte,
			'url_carte' => $url_carte,
			)
		);
	}
}
?>