<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/*
 * Plugin Notifications
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */


// Fonction appelee par divers pipelines

// Notes :
// Actuellement la fonction ne peux envoyer de mails html que si l'expediteur est positionné (on n'utilise pas la config facteur)
// notifications_envoyer_mails() de spip ne peut pas envoyer de mails en html. Voir avec le plugin notifications avancees

function notifications_commande_instituer_dist($quoi, $id_commande, $options) {
	spip_log("notifications_commande_instituer_dist id_commande $id_commande",'commandes');
	include_spip('inc/config');
	$config = lire_config('commandes');

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("notifications_commande_instituer_dist : statut inchange ".$options['statut'],'commandes');
		return;
	}
	// Si les notifications sont désactivées
	if(!$config['activer']) {
		spip_log("notifications_commande_instituer_dist : notifications désactivées",'commandes');
		return;
	}

	// Envoie une notification si la commande passe dans un des statuts choisis dans la config
	if(!in_array($options['statut'],$config['quand'])) {
		spip_log("notifications_commande_instituer_dist : pas de notificationpour ce nouveau statut ".$options['statut'],'commandes');
		return;
	}

	include_spip('inc/texte');
	$id_type = id_table_objet("commande");
	$expediteur = "";
	$destinataires = array();
	
	//
	// Determiner l'expediteur
	//
	switch($config['expediteur']) {
		case 'webmaster' :
			$expediteur = commandes_email_auteur($config['expediteur_webmaster']) ;
		break;
		case 'administrateur' :
			$expediteur = commandes_email_auteur($config['expediteur_administrateur']) ;
		break;
		case 'email' :
			$expediteur = $config['expediteur_email'];
		break;
		default : // Expediteur de type facteur, on laisse $expediteur vide
		break;
	}


	//
	// Envoyer les emails au(x) vendeur(s)
	//
	switch($config['vendeur']) {
		case 'webmaster' :
			$destinataires = array_map("commandes_email_auteur",$config['vendeur_webmaster']) ;
		break;
		case 'administrateur' :
			$destinataires = array_map("commandes_email_auteur",$config['vendeur_administrateur']) ;
		break;
		case 'email' :
			$destinataires = explode(',',$config['vendeur_email']);
		break;
		default :
			spip_log("notifications_commande_instituer_dist Erreur choix config vendeur : ".$config['vendeur'],'commandes');
			return;
		break;
	}


	$destinataires = pipeline('notifications_destinataires',
										array(
											'args'=>array('quoi'=>$quoi,'id'=>$id_commande,'options'=>$options),
											'data'=>$destinataires)
										);


	spip_log("notifications_commande_instituer_dist Expediteur $expediteur, Envoi au(x) vendeur(s) ".implode(", ", $destinataires),'commandes');
	$modele = "notifications/commande_vendeur";
	$texte = recuperer_fond($modele,array($id_type=>$id_commande,"id"=>$id_commande));
	notifications_nettoyer_emails($destinataires);
	// Si un expediteur est impose, on doit utiliser la fonction envoyer_email pour rajouter l'expediteur
	if($expediteur) {
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		$envoyer_mail($destinataires, _T('commandes:une_commande_sur', array('nom'=>$GLOBALS['meta']["nom_site"])), $texte, $expediteur,"Content-type: text/html");
	} else {
		notifications_envoyer_mails($destinataires, $texte);
	}

	//
	// Envoyer la notification au client
	//
	if($config['client']) {
		$id_auteur=$options['id_auteur'];
		if(!$id_auteur)
			$id_auteur=sql_getfetsel("id_auteur","spip_commandes","id_commande=".$id_commande);
		
		//envoyer un mail different pour le client		
		$mailclient = sql_getfetsel("email","spip_auteurs","id_auteur=".$id_auteur);
	
		if ($mailclient!=''){
			$modele = "notifications/commande_client";
	
			$destinataires = pipeline( 'notifications_destinataires',
												array(
													'args'=>array('quoi'=>$quoi,'id'=>$id_commande,'options'=>$options),
													'data'=>$mailclient)
													);
	
			spip_log("notifications_commande_instituer_dist Expediteur $expediteur, Envoi au client $mailclient",'commandes');
			$texte = recuperer_fond($modele,array($id_type=>$id_commande,"id"=>$id_commande));
			notifications_nettoyer_emails($destinataires);
			if($expediteur) {
				$envoyer_mail = charger_fonction('envoyer_mail','inc');
				$envoyer_mail($destinataires, _T('commandes:votre_commande_sur', array('nom'=>$GLOBALS['meta']["nom_site"])), $texte, $expediteur,"Content-type: text/html");
			} else {
				notifications_envoyer_mails($destinataires, $texte);
			}
		}
	}
}

?>