<?php

/***************************************************************************\
 *  SPIP propaganda, plugin SPIP d'envoi de carte postale électronique     *
 *                                                                         *
 *  Copyright (c) 2007                                                     *
 *  Quentin Drouet, Daniel Viñar Ulriksen                                  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

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
	charger_generer_url();
	
	$destinataire = _request('email_destinataire');
	$nom_destinataire = _request('nom_destinataire');
	$titre = _request('sujet');
	$texte = _request('texte_message_auteur');
	$auteur =_request('auteur');
	$document_carte = _request('document_carte');

	$previsualiser= _request('previsualiser');
	$valider= _request('valider');

	$type = lire_config('propaganda/droit_envoi');
	
	if ($type == "non") {
		if (!$GLOBALS["auteur_session"]) {
			return array('formulaires/login_forum', 0,
					array('inscription' => generer_url_public('identifiants'),
						'oubli' => generer_url_public('', 'action=pass')));
		} else {
		// forcer ces valeurs
			$nom_expediteur = $GLOBALS['auteur_session']['nom'];
			$adresse = $GLOBALS['auteur_session']['email'];
			$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		}
	}

	if (($type == "oui") || !$type) {
		$nom_expediteur = $GLOBALS['auteur_session']['nom'] ? $GLOBALS['auteur_session']['nom'] : _request('nom_expediteur');
		$adresse = $GLOBALS['auteur_session']['email'] ? $GLOBALS['auteur_session']['email'] : _request('email_expediteur');
		$id_auteur = $GLOBALS['auteur_session']['id_auteur']? $GLOBALS['auteur_session']['id_auteur'] : 0;
	}
	// doit-on envoyer le mail ?
	if ($valider) {

		spip_query("INSERT INTO spip_propaganda (id_auteur, id_document, titre, texte, email_destinataire, nom_destinataire, hash, confidentiel) VALUES ('$id_auteur', '$document_carte', "._q($titre).", "._q($texte).", "._q($destinataire).", "._q($nom_destinataire).", '$hash', '$confidentiel')");
		$id_propaganda = mysql_insert_id();

		$url = parametre_url(generer_url_public('carte'),'id_propaganda',$id_propaganda);


		$texte2 = ""._T('propaganda:bonjour')." $nom_destinataire".",\n\n$nom_expediteur ($adresse) "._T('propaganda:untel_envoi_carte')."\n\n";
		$texte2 .= "\n"._T('propaganda:consulter_carte')." \n$url\n\n";
		$texte2 .= _T('propaganda:son_message')."\n\n".$texte."\n\n";
		$texte2 .= "----------------\n$url\n\n"._T('propaganda:merci_de_visite')."\n";
		$texte2 .= "\n\n-- "._T('envoi_via_le_site')." ".supprimer_tags(extraire_multi(lire_meta('nom_site')))." (".lire_meta('adresse_site')."/) --\n";
		
		$titre2 = "[".supprimer_tags(extraire_multi(lire_meta('nom_site')))."] - ";
		$titre2 .= $titre;
		$titre2 = utf8_decode($titre2);
		envoyer_mail($destinataire, $titre2, $texte2, $nom_expediteur.' <'.$adresse.'>',
					"X-Originating-IP: ".$GLOBALS['REMOTE_ADDR']);

		$msg_envoye =  _T('propaganda:carte_envoyee');
		$msg_envoye .= ' - <a class="nouvel_envoi" href="';
		$msg_envoye .= generer_url_article($id_article);
		$msg_envoye .= '">'._T('propaganda:envoi_nouvelle_carte').'</a>';
		
		return $msg_envoye;
		
	}
	
	else{
		if($previsualiser)
		{

		if (!$adresse){$erreur .= _T('form_indiquer_email');}
		else if (!$nom_expediteur){$erreur .= _T('form_indiquer_nom');}
		else if (!$destinataire){$erreur .= _T('form_indiquer_destinataire');}
		else if (strlen($titre) < 3){$erreur .= _T('forum_attention_trois_caracteres');}
		else if (!email_valide($adresse)){$erreur .= _T('info_email_invalide');}
		else if (!$document_carte){$erreur .= _T('propaganda:choisissez_carte');}
		if(!$erreur){$bouton= _T('form_prop_confirmer_envoi');}


		$previsu = inclure_balise_dynamique(
			array(
				'formulaires/formulaire_propaganda_previsu',
				0,
				array(
					'url' => $url,
					'id_article' => $id_article,
					'mail' => $adresse,
					'nom_expediteur' => $nom_expediteur,
					'destinataire' => $destinataire,
					'nom_destinataire' => $nom_destinataire,
					'titre' => $titre,
					'texte' => $texte,
					'document_carte' => $document_carte,
					'erreur' => $erreur,
					'bouton' => $bouton,
				)
			),
			false);
				$previsu = preg_replace("@<(/?)f(orm[>[:space:]])@ism",
				"<\\1no-f\\2", $previsu);
		}
	return 
		array('formulaires/formulaire_propaganda', 0,
			array(
			'id_article' => $id_article,
			'mail' => $adresse,
			'nom_expediteur' => $nom_expediteur,
			'previsu' => $previsu,
			'destinataire' => $destinataire,
			'nom_destinataire' => $nom_destinataire,
			'titre' => $titre,
			'texte' => $texte,
			'document_carte' => $document_carte,
			)
		);
	}
}
?>
