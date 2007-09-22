<?php

//Fonction a appeller dans le script de retour de la banque
//si ce script n'est aps dans le spip on peut utiliser les commandes suivantes pour demmarer spip

# ou est l'espace prive de spip ?
//chdir('..');
//include('ecrire/inc_version.php');

// la banque renvoie l'identifiant de la transaction (l'id de l'abonne) et un statut de validation pour dire si le paiement est ok ou pas.

// $id_abonne est l'id dans spip_auteurs_elargis
// $validation_paiement est soit "ok", soit "erreur_bank"


// prevoir une table transactions, qui permettra d'avoir de bon id_transaction incrementes

function traiter_message_banque($produit,$id_abonne,$validation_paiement,$hash_article){

$abonne_res = spip_query("SELECT a.nom_famille, a.prenom, a.adresse, a.code_postal, a.ville, a.pays, a.telephone, a.commentaire, a.validite, b.email, b.id_auteur, b.alea_actuel, b.login , b.pass FROM `spip_auteurs_elargis` a, `spip_auteurs` b WHERE a.id='$id_abonne' AND a.id_auteur = b.id_auteur") ;

while($row = spip_fetch_array($abonne_res)){
$abonne = $row ;
}


if($produit == "abonnement"){
$abonnement_res = spip_query("SELECT a.duree, a.periode, a.montant, a.libelle FROM `spip_abonnements` a, `spip_auteurs_elargis_abonnements` b WHERE b.id_auteur_elargi = '$id_abonne' AND a.id_abonnement = b.id_abonnement") ;

while($abonnement = spip_fetch_array($abonnement_res)){
$libelle = $abonnement['libelle'];
$duree = $abonnement['duree'] ;
$periode = $abonnement['periode'] ;
}

$statut_abonnement = ($validation_paiement == "ok")? 'abonne' : 'prospect' ;

if($periode == "jours"){
$validite = ($validation_paiement == "ok") ? "DATE_ADD(CURRENT_DATE, INTERVAL ".$duree." DAY)" : "'".$abonne['validite']."'" ;
}elseif($periode == "mois"){
$validite = ($validation_paiement == "ok") ? "DATE_ADD(CURRENT_DATE, INTERVAL ".$duree." MONTH)" : "'".$abonne['validite']."'" ;
}

// fixer la date de validite et le statut de paiement, et des zones acces restreint selon l'abonnement a l'occasion
spip_query("UPDATE `spip_auteurs_elargis` SET statut_abonnement='$statut_abonnement', statut_paiement='$validation_paiement', validite = $validite WHERE id='$id_abonne'") ;
}

if($produit == "article"){
$article = spip_fetch_array(spip_query("SELECT a.titre, a.id_article FROM `spip_articles` a, `spip_auteurs_elargis_articles` b WHERE b.hash = '$hash_article' AND a.id_article = b.id_article") );
$libelle = $article['titre'];
spip_query("UPDATE `spip_auteurs_elargis_articles` SET statut_paiement='$validation_paiement' WHERE hash='$hash_article'") ;
}

	//envoyer un mail a l'admin et a l'abonne
	abonnement_envoyer_mails_confirmation($validation_paiement,$abonne,$libelle,$produit,$article);

if($validation_paiement == "ok")
return true ;
else
return false ;

}


function abonnement_envoyer_mails_confirmation($validation_paiement,$abonne,$libelle,$produit,$article=''){

	include_spip('inc/charsets');
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
	//$entetes .= "bcc: \n";          										// envoi en copie cachée à …


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
			
			// envoyer un lien pour choisir son mdp le ca echeant
			if($abonne['pass'] == ""){
			$message .=  "Votre identifiant de connexion au site est : ".$abonne['login']
			."\n\nCliquez le lien suivant pour choisir votre mot de passe"
			."\n".$adresse_site."/?page=inscription2_confirmation&id="
			.$abonne['id_auteur']."&cle=".$abonne['alea_actuel']."&mode=conf";
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

	
	}elseif($validation_paiement == "erreur_bank"){
	
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


function article_visible_par_abonne($id_auteur,$id_article){
$article_visible = spip_fetch_array(spip_query("SELECT a.id_article, a.statut_paiement FROM `spip_auteurs_elargis_articles` a, `spip_auteurs_elargis` b, `spip_auteurs` c WHERE c.id_auteur = '$id_auteur' AND b.id_auteur = c.id_auteur AND a.id_auteur_elargi = b.id AND a.id_article='$id_article'") );

if($article_visible['id_article'] == $id_article AND $article_visible['statut_paiement'] =="ok") 
	return true ;
else
	return false ;	

}

?>