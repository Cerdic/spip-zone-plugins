<?php

/**
 * Plugin Abonnement pour Spip 2.0
 * Licence GPL (c) 2009
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');


function abo_envoyer_mail_confirmation($validation_paiement,$id_abonne,$libelle,$produit,$id_produit='')
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$args = $securiser_action();

	// id_article-id_auteur-hash_article
	$args = explode('-',$args);
	
	if (count($args)!=2) {
		spip_log("action_envoyer_mail pas compris");
		// die("action_activer_article_dist pas compris");
	}
	

	include_spip('inc/charsets');
	include_spip('inc/filtres'); // pour email_valide(), sinon pas d'envoi...
	include_spip('inc/mail');
	
	$nom_expediteur = lire_config('abonnement/nom_envoi');
	$adresse_expediteur = lire_config('abonnement/email_envoi');
	
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
	
	// il faudrait ajouter  a.commentaire, s'il existe dans la bdd
	$r = sql_query("SELECT a.nom_famille, a.prenom, a.adresse, a.code_postal, a.ville, a.pays, a.telephone, b.email, b.id_auteur, b.login , b.pass FROM `spip_auteurs_elargis` a, `spip_auteurs` b WHERE a.id_auteur='$id_abonne' AND a.id_auteur = b.id_auteur") ; 
	$abonne = sql_fetch($r);
		
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
		// a revoir
		$message_ok = "Votre paiement est accepte" ;
		$message_ko = "Votre paiement est refuse" ;
	}

	if($validation_paiement == "ok"){
	
		$sujet = $sujet_message_ok ;
		$message= $message_ok."\n\n";
		
		// envoyer un lien pour choisir son mdp le cas echeant
		$adresse_site = $GLOBALS['meta']["adresse_site"];
		
		if($abonne['pass'] == ""){
	 	include_spip('inc/acces'); # pour creer_uniqid
		$cookie = creer_uniqid();
		// a revoir
		sql_updateq("spip_auteurs", array("cookie_oubli" => $cookie), "id_auteur=" . $abonne['id_auteur']);
		$message .=  "Votre identifiant de connexion au site est : ".$abonne['login']
		."\n\nCliquez le lien suivant pour choisir votre mot de passe"
		."\n".generer_url_public('spip_pass','p='.$cookie, true);
		}
				
		if($id_article && $abonne['pass'] == "")
			$message .= "\n\n Vous pourrez ensuite vous connecter et acceder a votre article en suivant ce lien \n\n"
			.$libelle." (".$adresse_site."/?page=article&id_article=".$id_article.")";
		
		if($id_article && $abonne['pass'] != "")
			$message .= "\n\n Identifiez-vous et accedez a votre article en suivant ce lien \n\n"
			.$libelle." (".$adresse_site."/?page=article&id_article=".$id_article.")";
		
			
		$message .= "\n\n".$nom_expediteur."\r\n";
		
	}elseif ($validation_paiement == "erreur_bank") {
			
		$sujet = $sujet_message_ko ;
		$message= $message_ko."\n\n\n".$nom_expediteur."\r\n";
		
	}	
	
	// envoyer mail de confirmation a l'abonné
	$adresse= $abonne['email'];
	
	envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
	// copie au webmestre
	envoyer_mail ('booz@rezo.net', $sujet, $message, $from = $expediteur, $headers = $entetes );
		
	
	// alerte webmestre
		//envoyer alerte au webmaster
		
		if($validation_paiement == "ok"){
			$sujet= "[".$nom_expediteur."-abo] Nouvel abonn&eacute; : ".$abonne['nom_famille'] ;
			$message = "Une nouvelle transaction a eu lieu ";
		}else{
			$sujet= "[".$nom_expediteur."-abo] Echec abonnement : ".$abonne['nom_famille'] ;
			$message = "Un abonn&eacute; n'a pas pu valider son abonnement (refus du paiement par la banque)";	
		}
						
		$message .= "\n\nNom : ".$abonne['nom_famille']."\nPr&eacute;nom : ".$abonne['prenom']."\n\nAdresse: \n".$abonne['adresse']."\n".$abonne['code_postal']." ".$abonne['ville']." ".$abonne['pays']
		."\n\nEmail : ".$abonne['email']
		."\nT&eacute;l&eacute;phone: ".$abonne['telephone']
		."\n\nCommentaire: ".$abonne['commentaire'];
		
		if($produit == "abonnement")				
		$message .= "\n\nAbonnement : ".$libelle ;

		if($produit == "article")				
		$message .= "\n\narticle : ".$libelle ;
		
		envoyer_mail ($adresse_expediteur, $sujet, $message, $from = $expediteur, $headers = $entetes );
		envoyer_mail ('booz@rezo.net', $sujet, $message, $from = $expediteur, $headers = $entetes );

	
// signaler un changement
	spip_log("abonnement: yeah","abonnement");
	
	return true;
}

?>
