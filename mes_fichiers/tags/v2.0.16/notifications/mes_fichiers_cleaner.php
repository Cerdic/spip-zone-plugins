<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_mes_fichiers_cleaner_dist($quoi, $id, $options) {
	include_spip('inc/config');
	$notif_active = (lire_config('mes_fichiers/notif_active', 'non') == 'oui');

	if ($notif_active) {
		/**
		 * On vérifie que l'on a bien supprimé au moins un fichier
		 */
		if (is_array($options['liste'])
		AND !empty($options['liste'])) {
			// preparation de la liste des destinataires
			include_spip('inc/mes_fichiers_utils');
			$destinataires = mes_fichiers_preparer_destinataires($quoi, $id, $options);

			// Construction du sujet du mail
			include_spip('inc/texte');
			$sujet_mail = "[" . typo($GLOBALS['meta']['nom_site'])
						. "][mes_fichiers] "
						. _T('mes_fichiers:message_cleaner_sujet');

			// Construction du texte du mail
			$duree = lire_config('mes_fichiers/duree_sauvegarde', 15);
			$liste_fichiers = "\n\r";
			foreach($options['liste'] as $_fichier){
				$liste_fichiers .= "- ${_fichier}\n\r";
			}
			$msg_mail = _T('mes_fichiers:message_notif_cleaner_intro', array('duree' => $duree)) . $liste_fichiers;

			// Envoi de la notification
			notifications_envoyer_mails($destinataires, $msg_mail, $sujet_mail);
		}
    }
}
?>