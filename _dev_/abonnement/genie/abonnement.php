<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_abonnement_dist($t){

// ---------------------------------------------------------------------------------------------
// Taches de fond
		
	spip_log("cron abonnement","abonnement");
		
	// fermer les zones aux echus
	// attention s'il y en a beaucoup
	$result = sql_select(
		"a.id_auteur",
		array(
			"spip_auteurs_elargis AS a",
			"spip_zones_auteurs AS b",
			"spip_auteurs_elargis_abonnements AS c"
		),
		array(
			"a.id_auteur = b.id_auteur",
			"a.id = c.id_auteur_elargi",
			"c.validite <> '0000-00-00 00:00:00'",
			"c.validite < " . date('Y-m-d H:i:s')
		),
	);
		
	while ($row = sql_fetch($result)){
		$id_auteur = $row['id_auteur'] ;
		spip_log("$id_auteur est echu (salo), il perd sa (ses) zone(s)", "abonnement");		
		sql_delete("spip_zones_auteurs", "id_auteur = " . sql_quote($id_auteur));
	}
		
	
	// relancer les abonnes
	// le cycle de relance comporte plusieurs phases
	// avant l'echeance (1 à 3 messages) , à mettre en relation avec un compte pre-approvisionné.
	// 1 après l'échéance traité ici.
	// ne pas envoyer plusieurs fois le meme message (flaguer un peu)
	
	$result = sql_select(array("libelle", "id_abonnement", "periode"),"spip_abonnements");
	
	while ($row = sql_fetch($result)){
		$libelle = $row['libelle'] ;
		$id_abonnement = $row['id_abonnement'] ;
		$periode = $row['periode'] ;
		
		// a décliner sur les 4 phases de relance
		
		if ($periode == "jours"){
			$validite = "DATE_ADD(CURRENT_DATE, INTERVAL 0 DAY)" ;
		} elseif ($periode == "mois") {
			$validite = "DATE_ADD(CURRENT_DATE, INTERVAL 0 DAY)" ;
		}
	
		$sujet_relance = lire_config("abonnement_relances/sujet_relance4") ;
		$texte_relance = lire_config("abonnement_relances/texte_relance4") ;
		$adresse_expediteur = lire_config("abonnement_relances/email_envoi") ;

		$result_abo = sql_select(
			array("a.id_auteur", "a.id", "c.email"),
			array(
				"spip_auteurs_elargis AS a",
				"spip_auteurs_elargis_abonnements AS b",
				"spip_auteurs AS c"
			),
			array(
				"a.id = b.id_auteur_elargi",
				"a.id_auteur = c.id_auteur",
				"b.id_abonnement = " . sql_quote($id_abonnement),
				"b.validite <> '0000-00-00 00:00:00'",
				"b.validite < " . $validite,
				"b.stade_relance < 4"
			),
		);

		while($row_abo = sql_fetch($result_abo)){
			$id_auteur = $row_abo['id_auteur'] ;
			$email_abonne = $row_abo['email'] ;
			$id_auteur_elargi = $row_abo['id'] ;
	
			spip_log($email_abonne."(".$id_auteur.") est a relancer\n","abonnement");
			spip_log($sujet_relance,"abonnement") ;
			
			include_spip('inc/filtres'); // pour email_valide(), sinon pas d'envoi...
			include_spip('inc/mail'); 
						
			if(envoyer_mail($email_abonne, $sujet_relance, $texte_relance, $adresse_expediteur)){
				sql_updateq("spip_auteurs_elargis_abonnements", array("stade_relance"=>4), "id_auteur_elargi = " . sql_quote($id_auteur_elargi));
				spip_log("relance faite","abonnement") ;
			}
		}
	}
	
	return 1; 
}
?>
