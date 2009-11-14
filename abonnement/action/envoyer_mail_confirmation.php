<?php

/**
 * Plugin Abonnement pour Spip 2.0
 * Licence GPL (c) 2009
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/* 
 * -
 * - 
 */
	
function abonnement_envoyer_mails_confirmation($validation_paiement,$id_auteur,$libelle,$produit,$article=''){

	include_spip('inc/charsets');
	include_spip('inc/filtres'); // pour email_valide(), sinon pas d'envoi...
	include_spip('inc/mail');
	
	$nom_expediteur = lire_config('abonnement/nom_envoi');
	$adresse_expediteur = lire_config('abonnement/email_envoi');
	
	$abonne = sql_fetsel('*', 'spip_auteurs_elargis a, spip_auteurs b', 'a.id_auteur = b.id_auteur and a.id_auteur = ' . sql_quote($id_auteur)) ;
	
	spip_log("Preparer le mail, prise en compte de l'abonne " .$abonne['nom_famille'] ." ".$abonne['email']." ".$abonne['id_auteur'],"abonnement");
	
	
	
	
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
	
	$expediteur = '"'.$nom_expediteur.'" <'.$adresse_expediteur.'>';
					
	$entete .= "Reply-To: ".$adresse_expediteur."\n";     					 // réponse
	$entete .= "MIME-Version: 1.0\n";
	$entete .= "Content-Type: text/plain; charset=$charset\n";	// Type Mime pour un message au format HTML
	$entete .= "Content-Transfer-Encoding: 8bit\n";
	$entete .= "X-Mailer: PHP/" . phpversion();         			// mailer
	//$entetes .= "Return-Path: < webmaster@ >\n"; 					// En cas d' erreurs 
	//$entetes .= "Errors-To: < webmaster@ >\n";    					// En cas d' erreurs 
	//$entetes .= "cc:  \n"; 											// envoi en copie à 
	//$entetes .= "bcc: booz@rezo.net\n";          										// envoi en copie cachée à 


	if($validation_paiement == "ok"){
	
	spip_log("Preparer le mail, paiement validé " .$abonne['nom_famille'] ." ".$abonne['email']." ".$abonne['id_auteur'],"abonnement");
	
	
		//au webmaster
		$message = "Une nouvelle transaction a eu lieu :\n\nNom : ".$abonne['nom_famille']."\nPr&eacute;nom : ".$abonne['prenom']."\n\nAdresse: \n".$abonne['adresse']."\n".$abonne['code_postal']." ".$abonne['ville']." ".$abonne['pays']
		."\n\nEmail : ".$abonne['email']
		."\nT&eacute;l&eacute;phone: ".$abonne['telephone']
		."\n\nCommentaire: ".$abonne['commentaire'];
		
		if($produit == "abonnement")				
		$message .= "\n\nAbonnement : ".$libelle ;

		if($produit == "article")				
		$message .= "\n\narticle : ".$libelle ;
		
		// Mail de reporting au webmaster
		envoyer_mail ( $adresse_expediteur, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		// Preparer le mail de confirmation au demandeur
		$adresse_site = $GLOBALS['meta']["adresse_site"];
		$sujet = $sujet_message_ok ;
		$message= $message_ok."\n\n";
		
		// résumé de l'abonnement
		if($produit == "abonnement"){	
		$message .=  "Votre abonnement : ".textebrut($libelle)."\n\n" ;
		}
		
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
			$message .= "\n\n Vous pourrez ensuite vous connecter et accéder à votre article en suivant ce lien \n\n"
			.$article['titre']." (".$adresse_site."/?page=article&id_article=".$article['id_article'].")";
		
		if($article['titre'] && $abonne['pass'] != "")
			$message .= "\n\n Vous pouvez accéder à votre article en suivant ce lien \n\n"
			.$article['titre']." (".$adresse_site."/?page=article&id_article=".$article['id_article'].")";
		
		if($produit == "abonnement")
		$message .=  "A tout moment, vous pouvez consulter les informations concernant votre abonnement sur la page : ".$adresse_site."/?page=mon_compte\n\n" ;

			
		$message .= "\n\n".$nom_expediteur."\r\n";
		
		// Envoyer la confirmation à l'abonné
		$adresse = $abonne['email'];
		spip_log("mail -> " .$abonne['nom_famille'] ." ".$adresse." ".$abonne['id_auteur']." ".$message." ".$sujet,"abonnement");
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
	
	}

	else {
	
		$sujet= "[".$nom_expediteur."-abo] Echec abonnement : ".$abonne['nom_famille'] ;

		$message = "Un abonn&eacute; n'a pas pu valider son abonnement (refus du paiement par la banque) :\n\nNom : ".$abonne['nom_famille']."\nPr&eacute;nom : ".$abonne['prenom']."\n\nAdresse : \n".$abonne['adresse']."\n".$abonne['code_postal']." ".$abonne['ville']." ".$abonne['pays']
		."\n\nEmail : ".$abonne['email']
		."\nT&eacute;l&eacute;phone : ".$abonne['telephone']
		."\n\nCommentaire : ".$abonne['commentaire'];
		
		if($produit == "abonnement")				
		$message .= "\n\nAbonnement : ".$libelle ;

		if($produit == "article")				
		$message .= "\n\narticle : ".$libelle ;
		
		// Mail reporting echec au webmaster
		envoyer_mail ( $adresse_expediteur, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		// mail de notification de l'echec au demandeur
		$sujet = $sujet_message_ko ;
		$message= $message_ko."\n\n\n".$nom_expediteur."\r\n";
		
		$adresse= $abonne['email'];
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
	}
	
	// signaler un changement
	spip_log("abonnement: mail envoyé. $libelle pour auteur " .$abonne['id_auteur'],"abonnement");
	
	return true;

}


?>
