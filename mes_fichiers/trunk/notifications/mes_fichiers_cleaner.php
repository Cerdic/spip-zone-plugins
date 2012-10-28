<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_mes_fichiers_cleaner_dist($quoi, $id, $options) {
	include_spip('inc/config');
	$notif_active = (lire_config('mes_fichiers/notif_active', 'non') == 'oui');

	if ($notif_active) {
		/**
		 * On vérifie que l'on a bien supprimé au moins un fichier
		 */
		if(is_array($options['liste'] && !empty($options['liste']))) {

			// pour typo()
			include_spip('inc/texte');

			$mails = lire_config('mes_fichiers/notif_mail');
			$tous = explode(',', $mails);
			$tous[] = $GLOBALS['meta']['email_webmaster'];
			$destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options),
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
			$duree = lire_config('mes_fichiers/duree_sauvegarde', 15);
			$msg_mail = _T('mes_fichiers:message_notif_cleaner_intro', array('duree' => $duree)) . $liste_fichiers;
			notifications_envoyer_mails($destinataires, $msg_mail, $sujet_mail);
		}
    }
}
?>
