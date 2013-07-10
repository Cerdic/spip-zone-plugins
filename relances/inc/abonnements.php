<?php
/**
 * Plugin Abonnements
 * (c) 2012-2013 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Envoyer un courriel à l'abonné pour lui rappeler combien de temps il lui reste
 */
function abonnements_notifier_echeance($id_abonnement, $id_relance, $id_auteur, $titre, $nom, $email, $duree, $periode, $format_envoi){
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
	
	$envoyer_mail(
		$email,
		$titre,
		recuperer_fond(
			'notifications/abonnement_echeance',
			array(
				'id_abonnement' => $id_abonnement,
				'id_relance' => $id_relance,
				'nom' => $nom,
				'email' => $email,
				'duree' => $duree,
				'periode' => $periode,
				'format_envoi' => $format_envoi,
			)
		),
		"", //ici le from
		"Content-Type: text/$format_envoi"
	);
	
	//on archive cet envoi dans la table relances_archives
	$archiver=sql_insertq("spip_relances_archives",array("id_relance"=>$id_relance,'id_abonnement' => $id_abonnement,"id_auteur"=>$id_auteur,"date"=>"NOW()"));
	
	
}

?>
