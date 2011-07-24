<?php

/**
 * Plugin abonnement pour Spip 2.0
 * Licence GPL (c) 2011
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
	
function abonnement_envoyer_mails_confirmation($validation_paiement,$id_auteur,$titre,$produit,$id_article=''){

	include_spip('inc/charsets');
	include_spip('inc/filtres'); // pour email_valide(), sinon pas d'envoi...
	include_spip('inc/mail');
	
	$nom_expediteur = lire_config('abonnement/nom_envoi');
	$expediteur = lire_config('abonnement/email_envoi');
		
	if (
		$id_auteur > 0
		and $email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur = '.intval($id_auteur))
		and $abonne = sql_fetsel(
			'*',
			'spip_contacts_liens LEFT JOIN spip_contacts USING(id_contact)',
			array(
				'objet = '.sql_quote('auteur'),
				'id_objet = '.intval($id_auteur)
			)
		)
	)
	if (_DEBUG_ABONNEMENT) spip_log("suite = $validation_paiement,$id_auteur,$titre,$produit,$id_article, nom_famille=".$abonne['nom'] ." email=".$email." id_auteur=".$id_auteur,"abonnement");
	else return;
	
	$adresse_site = $GLOBALS['meta']["adresse_site"];
	$nom_site = $GLOBALS['meta']["nom_site"];
	
	//appel de la fonction pour facteur
	$envoyer_mail = charger_fonction('envoyer_mail','inc/');

	$login = sql_getfetsel('login', 'spip_auteurs', 'id_auteur = '.intval($id_auteur));
	$adresse = sql_fetsel(
			'*',
			'spip_adresses_liens LEFT JOIN spip_adresses USING(id_adresse)',
			array(
				'objet = '.sql_quote('auteur'),
				'id_objet = '.intval($id_auteur)
			)
		);
	$numero = sql_fetsel(
			'*',
			'spip_numeros_liens LEFT JOIN spip_numeros USING(id_numero)',
			array(
				'objet = '.sql_quote('auteur'),
				'id_objet = '.intval($id_auteur)
			)
		);
	
	//$produit = abonnement ou article
		$sujet= "[".$nom_expediteur."-abo] Nouvel achat d'$produit par ".$abonne['nom'] ;
		$sujet_message_ok = lire_config('abonnement/sujet_ok');
		$sujet_message_ko = lire_config('abonnement/sujet_ko');
		$message_ok = lire_config('abonnement/texte_ok');
		$message_ko = lire_config('abonnement/texte_ko');
	
	
	//$expediteur = '"'.$nom_expediteur.'" <'.$adresse_expediteur.'>';
	
	
	$info_client="<br />$produit : $titre<br />"
		."<br />Nom : ".$abonne['nom']
		."<br />Pr&eacute;nom : ".$abonne['prenom']
		."<br />Adresse:"
		."<br />".$adresse['voie']." ".$adresse['complement']
		."<br />".$adresse['code_postal']." ".$adresse['ville']." ".$adresse['pays']
		."<br />Email : ".$email
		."<br />T&eacute;l&eacute;phone: ".$numero['numero']
		."<br />Descriptif: ".$abonne['descriptif'];
	
	

	if($validation_paiement == "paye"){
	
	if (_DEBUG_ABONNEMENT) spip_log("Mail depuis $expediteur paiement par ".$abonne['nom']." mail ".$email." Num ".$id_auteur,"abonnement");
		
	//Au webmaster
		$message_webmaster = "Une nouvelle transaction a eu lieu sur $nom_site <br />$info_client";
		
		$body = array('html'=>$message_webmaster); 

		$ok = $envoyer_mail($adresse_expediteur, $sujet, $body, $expediteur, $entetes);
		
	//Au client
		$sujet = $sujet_message_ok ;
		$message = 'Bienvenue '.$abonne['civilite'].' '.$abonne['prenom'].' '.$abonne['nom'].'<br />';
		$message .= $message_ok."<br />";
		
		// resume 
		if($produit == "abonnement"){	
		$message .=  "<br /><br />Votre abonnement sur $nom_site : ".textebrut($titre)."<br />" ;
		$produit_is_abonnement=true;
		}
		
		if($produit == "article"){
		$produit_is_article=true;
		}
		
		// envoyer un lien pour choisir son mdp le cas echeant
		if($abonne['pass'] == ""){
			include_spip('inc/acces'); # pour creer_uniqid
			$cookie = creer_uniqid();
			sql_updateq("spip_auteurs", array("cookie_oubli" => $cookie), "id_auteur=$id_auteur");
		$message .=  "<br />Votre identifiant de connexion au site est : $login<br />"
		."<br />Cliquez sur le lien suivant pour choisir votre mot de passe "
		."<br />".generer_url_public('spip_pass','p='.$cookie, true)."<br />";
		}
		
		if($produit_is_article){
			if($abonne['pass'] == "")
				$message .= "<br />Vous pourrez ensuite vous connecter et acceder a votre article en suivant ce lien";
			
			if($abonne['pass'] != "")
				$message .= "<br />Vous pouvez acceder a votre article en suivant ce lien";
			
			$message .="<br />".$article['titre'].' '.url_absolue(generer_url_public('article','id_article='.$id_article));
		}
		
		if($produit_is_abonnement)
		$message .=  "<br />A tout moment, vous pouvez consulter les informations concernant votre abonnement sur la page : "
		.$adresse_site."/?page=mon_compte" ;

			
		$message .= "<br /><br />".$nom_expediteur."<br />";
		
		$body = array(
		'html'=>$message,
		); 
		
		// Envoyer la confirmation a l'abonne 
		$ok = $envoyer_mail($email, $sujet, $body, $expediteur, $entetes);
	
	}

	else {	//echec du paiement
	
		$sujet= "[".$nom_expediteur."-abo] Echec abonnement : ".$abonne['nom'] ;
		$message = "Un abonn&eacute; n'a pas pu valider son abonnement (refus du paiement par la banque) <br />".$info_client;
		
		$body = array(
		'html'=>$message,
		); 
		
		// Mail reporting echec au webmaster
		$ok = $envoyer_mail($adresse_expediteur, $sujet, $body, $expediteur, $entetes);
		
		// mail de notification de l'echec au client
		$sujet = $sujet_message_ko;
		$message= $message_ko."<br /><hr />".$nom_expediteur."<br /><br />";
		
		$body = array(
		'html'=>$message,
		); 
		
		$ok = $envoyer_mail($email, $sujet, $body, $expediteur, $entetes);
	}
	
	// signaler un changement
	if (_DEBUG_ABONNEMENT) spip_log("$produit: mail envoye. $titre pour auteur " .$id_auteur,"abonnement");

if($ok) return true;	

}

?>
