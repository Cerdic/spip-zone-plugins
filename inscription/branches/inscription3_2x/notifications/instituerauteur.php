<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Notifications au changement de statut d'un auteur
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Notifier lors du changement de statut d'un auteur
 * 
 * Basée sur : 
 * http://doc.spip.org/@notifications_instituerarticle_dist
 * 
 * @param string $quoi
 * @param int $id_auteur
 * @param array $options
 */
function notifications_instituerauteur($quoi, $id_auteur, $options) {

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut auteur inchange",'notifications');
		return;
	}

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
	if ($options['statut_ancien'] == '8aconfirmer') {
		if($options['statut'] == '5poubelle'){
			$modele = "notifications/auteur_invalide";
			$modele_admin = "notifications/auteur_invalide_admin";
		}
		else{
			/**
			 * Dans le cas d'une validation, on envoit le pass
			 * On fait tout en php pour ne pas avoir de traces du pass dans les logs
			 * On regénère le mot de passe également
			 */
			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			$adresse_site = $GLOBALS['meta']["adresse_site"];
			$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
			
			$user = sql_fetsel('*','spip_auteurs','id_auteur='.intval($id_auteur));
			include_spip('inc/acces');
			$pass = creer_pass_aleatoire(8, $id_auteur);
			include_spip('action/editer_auteur');
			instituer_auteur($id_auteur, array('pass'=>$pass));

			$texte = "[$nom_site_spip] "._T('form_forum_identifiants')."\n\n"
					._T('form_forum_message_auto')."\n\n"
					. _T('form_forum_bonjour', array('nom'=>$user['nom']))."\n\n"
					. _T('form_forum_voici1', array('nom_site_spip' => $nom_site_spip,
					'adresse_site' => $adresse_site . '/',
					'adresse_login' => generer_url_public('login'))) . "\n\n- "
					. _T('form_forum_login')." " . $user['login'] . "\n- "
					. _T('form_forum_pass'). " " . $pass . "\n\n";
					
			$modele_admin = "notifications/auteur_valide_admin";
		}
	}

	if ($modele OR $texte){
		$options['type'] = 'user';
		$destinataires = array();

		$destinataires = pipeline('notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_auteur,'options'=>$options)
			,
				'data'=>$destinataires)
		);
		if($modele){
			$texte = email_notification_objet($id_auteur,"auteur",$modele);
		}
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