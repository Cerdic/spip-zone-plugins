<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_saveauto_cleaner_dist($quoi, $id, $options) {
	include_spip('inc/config');
	$notif_active = (lire_config('saveauto/notif_active') == 'oui');

	if ($notif_active) {
		/**
		 * On vérifie que l'on a bien supprimé au moins un fichier
		 */
		if (is_array($options['liste'])
		AND !empty($options['liste'])) {
			// preparation de la liste des destinataires
			$preparer = charger_fonction('preparer_destinataires', 'inc');
			$destinataires = $preparer($quoi, $id, $options);

			// Construction du sujet du mail
			include_spip('inc/texte');
			$sujet_mail = "[" . typo($GLOBALS['meta']['nom_site'])
						. "][saveauto] "
						. _T('saveauto:message_cleaner_sujet');

			// Construction du texte du mail
			$duree = lire_config('saveauto/jours_obso');
			$liste_fichiers = "\n\r";
			foreach($options['liste'] as $_fichier){
				$liste_fichiers .= "- ${_fichier}\n\r";
			}
			$msg_mail = _T('saveauto:message_notif_cleaner_intro', array('duree' => $duree)) . $liste_fichiers;

			// Envoi de la notification en utilisant la fonction interne qui n'utilise pas job_queue et permet l'envoi
			// de pièces jointes
			$envoyer = charger_fonction('envoyer_notification', 'inc');
			$envoyer($destinataires, $msg_mail, $sujet_mail);
		}
    }
}
?>
