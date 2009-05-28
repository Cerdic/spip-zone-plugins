<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_abonnement_dist($t){

// ---------------------------------------------------------------------------------------------
// Taches de fond
		
	spip_log("cron abonnement","abonnement");
		
	// fermer les zones aux echus
	// attention s'il y en a beaucoup
		
	$result = sql_query("
	SELECT a.id_auteur FROM spip_auteurs_elargis a, spip_zones_auteurs b, spip_auteurs_elargis_abonnements c
	WHERE
	a.id_auteur = b.id_auteur
	and a.id = c.id_auteur_elargi
	and c.validite <> '0000-00-00 00:00:00' 
	and c.validite < NOW()
	");
		
		while($row = sql_fetch($result)){
		$id_auteur = $row['id_auteur'] ;
		spip_log("$id_auteur est echu (salo), il perd sa (ses) zone(s)", "abonnement_coupures");		
		spip_query("DELETE FROM `spip_zones_auteurs` WHERE id_auteur='$id_auteur'");
		// enlever le statut abonné ?
		// passer en echu puis sorti ?
		}
			
	// relancer les abonnes
	// le cycle de relance comporte plusieurs phases
	// avant et apres l'echeance (4 messages) , à mettre en relation avec un compte pre-approvisionné.
	// ne pas envoyer plusieurs fois le meme message en utilisant le champs stade relance	
	
	include_spip('inc/filtres'); // pour email_valide(), sinon pas d'envoi...
	include_spip('inc/mail'); 
	
	$monitor = array();
	$adresse_expediteur = lire_config("abonnement_relances/email_envoi") ;
	$expediteur = '"'.lire_config("abonnement_relances/nom_envoi").'"<'.$adresse_expediteur.'>';
	$entetes = "bcc: booz@rezo.net\n" ;

	// relance 1
	$sujet_relance = lire_config("abonnement_relances/sujet_relance1") ;
	$texte_relance = lire_config("abonnement_relances/texte_relance1") ;
	
	// trouver les abonnes en relance 1
	
	$result = sql_query("
	SELECT a.id_auteur, a.email, ae.id, c.validite FROM spip_auteurs a, spip_auteurs_elargis ae, spip_auteurs_elargis_abonnements c WHERE 	a.id_auteur = ae.id_auteur
	and ae.id = c.id_auteur_elargi	and ae.statut_abonnement='abonne'	and c.validite <> '0000-00-00 00:00:00' 	and c.validite > DATE_ADD(CURRENT_DATE, INTERVAL 6 DAY) 	and c.validite < DATE_ADD(CURRENT_DATE, INTERVAL 8 DAY) 
	and c.stade_relance < 1	order by c.validite desc 
	");
	
	spip_log($sujet_relance." (stade 1) ","abonnement_relance") ;
	
	while($row_abo = sql_fetch($result)){
			$id_auteur = $row_abo['id_auteur'] ;
			$email_abonne = $row_abo['email'] ;
			$validite = $row_abo['validite'] ;
			$id_auteur_elargi = $row_abo['id'] ;
				
			spip_log($email_abonne."(".$id_auteur.") est a relancer (stade 1)\n","abonnement_relance");
				
			if(envoyer_mail($email_abonne, $sujet_relance, $texte_relance, $expediteur, $entetes)){
				sql_query("
				UPDATE `spip_auteurs_elargis_abonnements` SET stade_relance = '1' WHERE id_auteur_elargi = '$id_auteur_elargi'");
				spip_log("relance faite","abonnement_relance") ;
			}
			$monitor['relance_1'][] = "$email_abonne ($id_auteur / $id_auteur_elargi) - échu le : $validite" ;

	}
	
	// relance 2
	$sujet_relance = lire_config("abonnement_relances/sujet_relance2") ;
	$texte_relance = lire_config("abonnement_relances/texte_relance2") ;

	
	// trouver les abonnes en relance 2
	
	$result = sql_query("
	SELECT a.id_auteur, a.email, ae.id, c.validite FROM spip_auteurs a, spip_auteurs_elargis ae, spip_auteurs_elargis_abonnements c WHERE 	a.id_auteur = ae.id_auteur
	and ae.id = c.id_auteur_elargi	and ae.statut_abonnement='abonne'	and c.validite <> '0000-00-00 00:00:00' 	and NOW() < DATE_ADD(c.validite, INTERVAL 1 DAY) 	and NOW() > DATE_ADD(c.validite, INTERVAL 0 DAY) 
	and c.stade_relance < 2	order by c.validite desc 
	");
	
	spip_log($sujet_relance." (stade 2) ","abonnement_relance") ;
	
	while($row_abo = sql_fetch($result)){
			$id_auteur = $row_abo['id_auteur'] ;
			$email_abonne = $row_abo['email'] ;
			$validite = $row_abo['validite'] ;
			$id_auteur_elargi = $row_abo['id'] ;
				
			spip_log($email_abonne."(".$id_auteur.") est a relancer (stade 2)\n","abonnement_relance");
			
			if(envoyer_mail($email_abonne, $sujet_relance, $texte_relance, $expediteur, $entetes)){
				sql_query("
				UPDATE `spip_auteurs_elargis_abonnements` SET stade_relance = '2' WHERE id_auteur_elargi = '$id_auteur_elargi'");
				spip_log("relance faite","abonnement") ;
			}
			$monitor['relance_2'][] = "$email_abonne ($id_auteur / $id_auteur_elargi) - échu le : $validite" ;

	}	
	
	// relance 3
	$sujet_relance = lire_config("abonnement_relances/sujet_relance3") ;
	$texte_relance = lire_config("abonnement_relances/texte_relance3") ;

	
	// trouver les abonnes en relance 3
	
	$result = sql_query("
	SELECT a.id_auteur, a.email, ae.id, c.validite FROM spip_auteurs a, spip_auteurs_elargis ae, spip_auteurs_elargis_abonnements c WHERE 	a.id_auteur = ae.id_auteur
	and ae.id = c.id_auteur_elargi	and ae.statut_abonnement='abonne'	and c.validite <> '0000-00-00 00:00:00' 	and NOW() < DATE_ADD(c.validite, INTERVAL 9 DAY) 	and NOW() > DATE_ADD(c.validite, INTERVAL 8 DAY) 
	and c.stade_relance < 3	order by c.validite desc 
	");
	
	spip_log($sujet_relance." (stade 3) ","abonnement_relance") ;
		
	while($row_abo = sql_fetch($result)){
			$id_auteur = $row_abo['id_auteur'] ;
			$email_abonne = $row_abo['email'] ;
			$validite = $row_abo['validite'] ;
			$id_auteur_elargi = $row_abo['id'] ;
				
			spip_log($email_abonne."(".$id_auteur.") est a relancer (stade 3)\n","abonnement");
			
			if(envoyer_mail($email_abonne, $sujet_relance, $texte_relance, $expediteur, $entetes)){
				sql_query("
				UPDATE `spip_auteurs_elargis_abonnements` SET stade_relance = '3' WHERE id_auteur_elargi = '$id_auteur_elargi'");
				spip_log("relance faite","abonnement") ;
			}
			
			$monitor['relance_3'][] = "$email_abonne ($id_auteur / $id_auteur_elargi) - échu le : $validite" ;

	}
	
	// relance 4
	$sujet_relance = lire_config("abonnement_relances/sujet_relance4") ;
	$texte_relance = lire_config("abonnement_relances/texte_relance4") ;

	
	// trouver les abonnes en relance 4
	
	$result = sql_query("
	SELECT a.id_auteur, a.email, ae.id, c.validite FROM spip_auteurs a, spip_auteurs_elargis ae, spip_auteurs_elargis_abonnements c WHERE 	a.id_auteur = ae.id_auteur
	and ae.id = c.id_auteur_elargi	and ae.statut_abonnement='abonne'	and c.validite <> '0000-00-00 00:00:00' 	and NOW() < DATE_ADD(c.validite, INTERVAL 30 DAY) 	and NOW() > DATE_ADD(c.validite, INTERVAL 29 DAY) 
	and c.stade_relance < 4	order by c.validite desc 
	");

	spip_log($sujet_relance." (stade 4) ","abonnement_relance") ;
		
	while($row_abo = sql_fetch($result)){
			$id_auteur = $row_abo['id_auteur'] ;
			$email_abonne = $row_abo['email'] ;
			$validite = $row_abo['validite'] ;
			$id_auteur_elargi = $row_abo['id'] ;
				
			spip_log($email_abonne."(".$id_auteur.") est a relancer (stade 4)\n","abonnement_relance");
			
			if(envoyer_mail($email_abonne, $sujet_relance, $texte_relance, $expediteur, $entetes)){
				sql_query("
				UPDATE `spip_auteurs_elargis_abonnements` SET stade_relance = '4' WHERE id_auteur_elargi = '$id_auteur_elargi'");
				spip_log("relance faite","abonnement") ;
			}
			$monitor['relance_4'][] = "$email_abonne ($id_auteur / $id_auteur_elargi) - échu le : $validite" ;

	}	
	
	$sujet_recap = "[opérateur de nuit] - relances effectuées" ;
	$recap = "Voici le détail des relances effectuées le ".date("Y-m-d H:i:s") ;
	foreach($monitor as $k => $v){
	$recap .= "\n\n$k :\n" ;
		foreach($v as $abo){
			$recap .= "\n- $abo";
		}
	}
	// mail de monitoring
	envoyer_mail($adresse_expediteur, $sujet_recap, $recap, $expediteur, $entetes);
	
		
	return 1;
}
?>
