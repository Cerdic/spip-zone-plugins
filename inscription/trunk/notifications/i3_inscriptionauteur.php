<?php
/**
 * Plugin Inscription3 pour SPIP
 * © cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Notifications d'inscription d'un auteur
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Notifier lors de l'inscription d'un auteur
 * 
 * @param string $quoi
 * @param int $id_auteur
 * @param array $options
 */
function notifications_i3_inscriptionauteur($quoi, $id_auteur, $options) {

	include_spip('inc/texte');
	include_spip('inscription3_mes_fonctions');

	$modele = "";
	
	/**
	 * Si l'ancien statut est 8aconfirmer
	 * - on notifie la validation s'il n'est pas mis à la poubelle
	 * - on notifie l'invalidation s'il est mis à la poubelle
	 * 
	 * S'il est validé, on lui recrée un pass que l'on met dans le mail avec son login
	 */
	if ($options['statut'] == '8aconfirmer') {
		$modele = "notifications/auteur_inscription_confirmer";
		$modele_admin = "notifications/auteur_inscription_confirmer_admin";
	}

	if ($options['statut_ancien'] == '8aconfirmer' && $options['statut'] != "poubelle") {
		$modele = "notifications/auteur_inscription_valider";
		$modele_admin = "notifications/auteur_valide_admin";
	}

	if (($options['statut'] != '8aconfirmer') && ($options['pass'] == 'ok')) {
		$modele = "notifications/auteur_inscription_pass";
	}
	/**
	 * Vérification régulière (via Cron) des comptes à valider ou invalider
	 */
	if($options['verifier_confirmer'] == 'oui'){
		$modele_admin = "notifications/auteur_inscription_verifier_admin";
	}
	if ($modele){
		$options['type'] = 'user';
		$destinataires = array();

		$destinataires = pipeline('notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_auteur,'options'=>$options)
			,
				'data'=>$destinataires)
		);
		$texte = email_notification_objet($id_auteur,"auteur",$modele);
		notifications_envoyer_mails($destinataires, $texte);
	}
	
	if ($modele_admin){
		$options['type'] = 'admin';
		$destinataires = array();

		$destinataires = pipeline('notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_auteur,'options'=>$options)
			,
				'data'=>$destinataires)
		);
		$texte = email_notification_objet($id_auteur,"auteur",$modele_admin);
		notifications_envoyer_mails($destinataires, $texte);
	}
}

?>