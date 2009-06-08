<?php

function abonnement_envoyer_mails_confirmation($validation_paiement,$abonne,$libelle,$produit,$article=''){

	include_spip('inc/charsets');
	include_spip('inc/filtres'); // pour email_valide(), sinon pas d'envoi...
	include_spip('inc/mail');
	
	$nom_expediteur = lire_config('abonnement/nom_envoi');
	$adresse_expediteur = lire_config('abonnement/email_envoi');
	
	if($produit == "abonnement"){				
		$sujet= "[".$nom_expediteur."-abo] Nouvel abonn&eacute; : ".$abonne['nom_famille'] ;
		$sujet_message_ok = lire_config('abonnement/sujet_ok');
		$sujet_message_ko = lire_config('abonnement/sujet_ko');
		$message_ok = lire_config('abonnement/texte_ok');
		$message_ko = lire_config('abonnement/texte_ko');
	}
	
	if($produit == "article"){				
		$sujet= "[".$nom_expediteur."-abo] Nouvel achat d'article : ".$abonne['nom_famille'] ;
		$sujet_message_ok = "Bravo pour votre article" ;
		$sujet_message_ko = "Pas de chance pour votre article" ;
		$message_ok = "Votre paiement est accepte" ;
		$message_ko = "Votre paiement est refuse" ;
	}
	
	$expediteur = $nom_expediteur.'<'.$adresse_expediteur.'>';
					
	$entete .= "Reply-To: ".$adresse_expediteur."\n";     					 // réponse
	$entete .= "MIME-Version: 1.0\n";
	$entete .= "Content-Type: text/plain; charset=$charset\n";	// Type Mime pour un message au format HTML
	$entete .= "Content-Transfer-Encoding: 8bit\n";
	$entete .= "X-Mailer: PHP/" . phpversion();         			// mailer
	//$entetes .= "Return-Path: < webmaster@ >\n"; 					// En cas d' erreurs 
	//$entetes .= "Errors-To: < webmaster@ >\n";    					// En cas d' erreurs 
	//$entetes .= "cc:  \n"; 											// envoi en copie à 
	//$entetes .= "bcc: \n";          										// envoi en copie cachée à 


	if($validation_paiement == "ok"){
	
		//au webmaster
		$message = "Une nouvelle transaction a eu lieu :\n\nNom : ".$abonne['nom_famille']."\nPr&eacute;nom : ".$abonne['prenom']."\n\nAdresse: \n".$abonne['adresse']."\n".$abonne['code_postal']." ".$abonne['ville']." ".$abonne['pays']
		."\n\nEmail : ".$abonne['email']
		."\nT&eacute;l&eacute;phone: ".$abonne['telephone']
		."\n\nCommentaire: ".$abonne['commentaire'];
		
		if($produit == "abonnement")				
		$message .= "\n\nAbonnement : ".$libelle ;

		if($produit == "article")				
		$message .= "\n\narticle : ".$libelle ;
		
		envoyer_mail ( $adresse_expediteur, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		// au demandeur
		$adresse_site = $GLOBALS['meta']["adresse_site"];
		$adresse= $abonne['email'];
		$sujet = $sujet_message_ok ;
		$message= $message_ok."\n\n";
		
		// envoyer un lien pour choisir son mdp le cas echeant
		if($abonne['pass'] == ""){
	 	include_spip('inc/acces'); # pour creer_uniqid
		$cookie = creer_uniqid();
		sql_updateq("spip_auteurs", array("cookie_oubli" => $cookie), "id_auteur=" . $abonne['id_auteur']);
		$message .=  "Votre identifiant de connexion au site est : ".$abonne['login']
		."\n\nCliquez le lien suivant pour choisir votre mot de passe"
		."\n".generer_url_public('spip_pass','p='.$cookie, true);
		}
		
		if($article['titre'] && $abonne['pass'] == "")
			$message .= "\n\n Vous pourrez ensuite vous connecter et acceder a votre article en suivant ce lien \n\n"
			.$article['titre']." (".$adresse_site."/?page=article&id_article=".$article['id_article'].")";
		
		if($article['titre'] && $abonne['pass'] != "")
			$message .= "\n\n Vous pouvez acceder a votre article en suivant ce lien \n\n"
			.$article['titre']." (".$adresse_site."/?page=article&id_article=".$article['id_article'].")";
		
			
		$message .= "\n\n".$nom_expediteur."\r\n";
		
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		envoyer_mail ('booz@rezo.net', $sujet, $message, $from = $expediteur, $headers = $entetes );
	
	}

	elseif ($validation_paiement == "erreur_bank") {
	
		$sujet= "[".$nom_expediteur."-abo] Echec abonnement : ".$abonne['nom_famille'] ;
		//au webmaster
		$message = "Un abonn&eacute; n'a pas pu valider son abonnement (refus du paiement par la banque) :\n\nNom : ".$abonne['nom_famille']."\nPr&eacute;nom : ".$abonne['prenom']."\n\nAdresse : \n".$abonne['adresse']."\n".$abonne['code_postal']." ".$abonne['ville']." ".$abonne['pays']
		."\n\nEmail : ".$abonne['email']
		."\nT&eacute;l&eacute;phone : ".$abonne['telephone']
		."\n\nCommentaire : ".$abonne['commentaire'];
		
		if($produit == "abonnement")				
		$message .= "\n\nAbonnement : ".$libelle ;

		if($produit == "article")				
		$message .= "\n\narticle : ".$libelle ;
		
		
		envoyer_mail ( $adresse_expediteur, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		// au demandeur
		$adresse= $abonne['email'];
		$sujet = $sujet_message_ko ;
		$message= $message_ko."\n\n\n".$nom_expediteur."\r\n";
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		envoyer_mail ('booz@rezo.net', $sujet, $message, $from = $expediteur, $headers = $entetes );
	}
}


?>
