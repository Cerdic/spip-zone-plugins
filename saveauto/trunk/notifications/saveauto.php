<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_saveauto_dist($quoi, $id, $options) {
	include_spip('inc/config');
	$notif_active = (lire_config('saveauto/notif_active', 'non') == 'oui');

	if ($notif_active
	AND !$options['err']) {
		// preparation de la liste des destinataires
		$preparer = charger_fonction('preparer_destinataires', 'inc');
		$destinataires = $preparer($quoi, $id, $options);

		// Determination de l'auteur de la sauvegarde
		if (intval($options['auteur'])) {
			$auteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur='.intval($options['auteur']));
		}else{
			$auteur = $options['auteur'];
		}

		// Construction du sujet du mail
		include_spip('inc/texte');
		$base = $GLOBALS['connexions'][0]['db'];
		$sujet_mail = "[" . typo($GLOBALS['meta']['nom_site'])
					. "][saveauto] "
					. _T('saveauto:message_sauver_sujet', array('base' => $base));

		// Construction du texte du mail
		$msg_mail = _T('saveauto:message_notif_sauver_intro', array('base' => $base, 'auteur' => $auteur));

		// Mise en pièce jointe de la sauvegarde si elle ne depasse pas le seuil défini
		$max_mail = lire_config('saveauto/mail_max_size');
		$pieces = array();
		if (filesize($options['chemin_fichier']) < $max_mail*1000*1000) {
			// Determination du mime-type
			$extension = pathinfo($options['chemin_fichier'], PATHINFO_EXTENSION);
			$mime_type = ($extension == 'zip') ? 'application/zip' : 'text/plain';
			$pieces[] = array(
						'chemin' => $options['chemin_fichier'],
						'nom' => basename($options['chemin_fichier']),
						'encodage' => 'base64',
						'mime' => $mime_type);
		}

		// Envoi de la notification
		$envoyer = charger_fonction('envoyer_notification', 'inc');
		$envoyer($destinataires, $msg_mail, $sujet_mail, $pieces);
    }
}

function notifications_saveauto_old_dist($quoi, $id, $options){
	$cfg = lire_config('saveauto');
	include_spip('inc/envoyer_mail');
	if(defined('_DIR_SITE')){
		$racine = _DIR_SITE;
	}else{
		$racine = _DIR_RACINE;
	}
	if (!empty($cfg['destinataire_save']) OR $options['err']) {
		/**
		 * Notifier une erreur dans la génération d'une sauvegarde
		 */
		$serveur = $_SERVER['SERVER_NAME'];
		if($options['err']){
			$tous = $cfg['destinataire_save'] ? $cfg['destinataire_save'] : $GLOBALS['meta']['email_webmaster'];
			$destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
				,
					'data'=>$tous)
			);
			$sujet_mail = "[".nettoyer_titre_email($GLOBALS['meta']['nom_site'])."] "._T('saveauto:erreur_mail_sujet')." "._T('saveauto:info_sql_base').$cfg['base'];
			$msg_mail = recuperer_fond('notifications/saveauto_nok',array('cfg'=>$cfg,'erreur'=>$options['err'],'serveur'=>$serveur));
			notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
		}
		/**
		 * La sauvegarde a apparemment fonctionné
		 */
		else{
			$filesize = (filesize($options['chemin_fichier']) / 1000000);
			$fichier = str_replace($racine,'',$options['chemin_fichier']);
			
			$tous = $cfg['destinataire_save'];
			
	        $sujet_mail = "[".nettoyer_titre_email($GLOBALS['meta']['nom_site'])."] "._T('saveauto:titre_saveauto')." "._T('saveauto:info_sql_base').$cfg['base'];
	        $msg_mail = recuperer_fond('notifications/saveauto_ok',array('cfg'=>$cfg,'filesize'=>$filesize,'serveur'=>$serveur,'fichier_racine'=>$fichier));
	        $destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
				,
					'data'=>$tous)
			);
			if((filesize($options['chemin_fichier']) / 1000000) < lire_config('saveauto/mail_max_size')){
				$corps['texte'] = $msg_mail;
				$corps['pieces_jointes'][0] = array(
					'chemin'=>$options['chemin_fichier'],
					'nom'=>basename($options['chemin_fichier']),
					'encodage' => 'base64',
					'mime' => ''
				);
			}else{
				$corps = $msg_mail;
				
				//$corps .= _T('saveauto:erreur_mail_fichier_lourd',array('fichier'=>$fichier));
			}
			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			$envoyer_mail($destinataires, $sujet_mail, $corps, $from = "", $headers = "");
		}
    }
}
?>