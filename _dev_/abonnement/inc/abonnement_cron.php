<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function cron_abonnement_cron($t){

// ---------------------------------------------------------------------------------------------
// Taches de fond
		
	spip_log("cron abonnement","abonnement");
		
	// fermer les zones aux echus
	// attention s'il y en a beaucoup
		
	$result = spip_query("
	SELECT a.id_auteur FROM spip_auteurs_elargis a, spip_zones_auteurs b, spip_auteurs_elargis_abonnements c
	WHERE
	a.id_auteur = b.id_auteur
	and a.id = c.id_auteur_elargi
	and c.validite <> '0000-00-00 00:00:00' 
	and c.validite < NOW()
	");
		
		while($row = spip_fetch_array($result)){
		$id_auteur = $row['id_auteur'] ;
		spip_log("$id_auteur est echu (salo), il perd sa (ses) zone(s)", "abonnement");		
		spip_query("DELETE FROM `spip_zones_auteurs` WHERE id_auteur='$id_auteur'");
		}
		
	
	// relancer les abonnes
	// le cycle de relance comporte plusieurs phases
	// avant l'echeance (1 à 3 messages) , à mettre en relation avec un compte pre-approvisionné.
	// 1 après l'échéance traité ici.
	// ne pas envoyer plusieurs fois le meme message (flaguer un peu)
	

	
	$result = spip_query("
	SELECT libelle, id_abonnement, periode FROM spip_abonnements 
	");
		
		while($row = spip_fetch_array($result)){
		$libelle = $row['libelle'] ;
		$id_abonnement = $row['id_abonnement'] ;
		$periode = $row['periode'] ;
		
		// a décliner sur les 4 phases de relance
		
		if($periode == "jours"){
	 	$validite = "DATE_ADD(CURRENT_DATE, INTERVAL 0 DAY)" ;
	 	}elseif($periode == "mois"){
	 	$validite = "DATE_ADD(CURRENT_DATE, INTERVAL 0 DAY)" ;
	 	}
	 	
	 	$sujet_relance = lire_config("abonnement_relances/sujet_relance4") ;
	 	$texte_relance = lire_config("abonnement_relances/texte_relance4") ;
		$adresse_expediteur = lire_config("abonnement_relances/email_envoi") ;

		$result_abo = spip_query("
		SELECT a.id_auteur, a.id, c.email FROM spip_auteurs_elargis a, spip_auteurs_elargis_abonnements b, spip_auteurs c
		WHERE
		a.id = b.id_auteur_elargi
		and a.id_auteur = c.id_auteur
		and b.id_abonnement = '$id_abonnement'
		and b.validite <> '0000-00-00 00:00:00'
		and b.validite < $validite
		and b.stade_relance < 4
		");
		

			while($row_abo = spip_fetch_array($result_abo)){
			$id_auteur = $row_abo['id_auteur'] ;
			$email_abonne = $row_abo['email'] ;
			$id_auteur_elargi = $row_abo['id'] ;
	
			spip_log($email_abonne."(".$id_auteur.") est a relancer\n","abonnement");
			spip_log($sujet_relance,"abonnement") ;
			
			include_spip('inc/filtres'); // pour email_valide(), sinon pas d'envoi...
			include_spip('inc/mail'); 
						
			if(envoyer_mail($email_abonne, $sujet_relance, $texte_relance, $adresse_expediteur)){
				spip_query("UPDATE `spip_auteurs_elargis_abonnements` SET stade_relance = 4 WHERE id_auteur_elargi = '$id_auteur_elargi'");
			spip_log("relance faite","abonnement") ;
			}

			}


		}

	
		
	return 1; 
}
?>