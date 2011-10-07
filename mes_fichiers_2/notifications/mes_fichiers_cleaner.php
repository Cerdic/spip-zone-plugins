<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_mes_fichiers_cleaner_dist($quoi, $id, $options){
	$cfg = lire_config('mes_fichiers');
	if ($cfg['notif_active'] == 'oui') {
		/**
		 * On vérifie que l'on a bien supprimé au moins un fichier
		 */
		if(is_array($options['liste'] && !empty($options['liste']))){
			$tous = explode(',',$cfg['notif_mail']);
			$tous[] = $GLOBALS['meta']['email_webmaster'];
			$destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
				,
					'data'=>$tous)
			);

			/**
			 * Nettoyage de la liste d'emails en vérifiant les doublons
			 * et la validité des emails
			 */
			notifications_nettoyer_emails($destinataires);
			$liste_fichiers = "\n\r";
			foreach($liste as $fichier){
				$liste_fichiers = "- ".$fichier."\n\r";
			}
			$sujet_mail = "[".typo($GLOBALS['meta']['nom_site'])."] "._T('mes_fichiers:message_cleaner_sujet');
			$msg_mail = _T('mes_fichiers:message_notif_cleaner_intro',array('frequence' => $cfg['duree_sauvegarde']))
					.$liste_fichiers;
			notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
		}
    }
}
?>