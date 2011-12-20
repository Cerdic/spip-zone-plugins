<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/*
 * Plugin Notifications
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */


// Fonction appelee par divers pipelines

function notifications_commande_instituer_dist($quoi, $id_commande, $options) {
	include_spip('inc/config');
	$config = lire_config('commandes');

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("notifications_commande_instituer_dist : statut inchange",'notifications');
		return;
	}
	// Si les notifications sont désactivées
	if(!$config['activer']) {
		spip_log("notifications_commande_instituer_dist : notifications désactivées",'notifications');
		return;
	}

	// Envoie une notification si la commande passe dans un des statuts choisis dans la config
	if($options['statut']!in_array($config['quand'])) return;

	include_spip('inc/texte');
	$id_type = id_table_objet("commande");
	$destinataires = array();
	
	$query = sql_select("email","spip_auteurs","statut = '0minirezo'");

	// notifier uniquement les webmestres ?
	if ($GLOBALS['notifications']['inscription'] == 'webmestres') {
		$query = sql_select("email","spip_auteurs","statut = '0minirezo' AND webmestre = 'oui'");
	}

	while ($row = sql_fetch($query)) {
		$destinataires[] = $row["email"];
		//spip_log("notifications_commande_instituer_dist mailto webmasters ".$row["email"],'commande');
	}

	switch($config['vendeur']) {
		case 'webmaster' :
		break;
		case 'administrateur' :
		break;
		case 'email' :
			$destinataires = $config['vendeur_email'] ;
		break;
		default :
			spip_log("notifications_commande_instituer_dist Erreur choix config vendeur : ".$config['vendeur'],'commande');
			return;
		break;
	}
	
	$destinataires = pipeline('notifications_destinataires',
		array(
			'args'=>array('quoi'=>$quoi,'id'=>$id_commande,'options'=>$options),
			'data'=>$destinataires)
	);

	//
	// Envoyer les emails au(x) vendeur(s)
	//
	$modele = "notifications/commande_vendeur";
	foreach ($destinataires as $email) {
		$texte = recuperer_fond($modele,array($id_type=>$id_commande,"id"=>$id_commande));
		notifications_envoyer_mails($email, $texte);
		spip_log("notifications_commande_instituer_dist mailto vendeur ".$email,'commande');
	}

	// puis on recherche l'auteur de la commande
	$id_auteur=$options['id_auteur'];
	if(!$id_auteur)
		$id_auteur=sql_getfetsel("id_auteur","spip_commandes","id_commande=".$id_commande);
	
	//envoyer un mail different pour le client		
	$mailclient = sql_getfetsel("email","spip_auteurs","id_auteur=".$id_auteur);

	if ($mailclient!=''){
		$modele = "notifications/commande_client";

		$destinataires = pipeline('notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_commande,'options'=>$options),
				'data'=>$mailclient)
		);
		spip_log("notifications_commande_instituer_dist mailto client ".$mailclient,'commande');

		$texte = recuperer_fond($modele,array($id_type=>$id_commande,"id"=>$id_commande));
		notifications_envoyer_mails($destinataires, $texte);
	}
}

?>