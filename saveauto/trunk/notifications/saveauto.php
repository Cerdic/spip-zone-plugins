<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_saveauto_dist($quoi, $id, $options) {
	include_spip('inc/config');
	$notif_active = (lire_config('saveauto/notif_active') == 'oui');

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

		// Envoi de la notification en utilisant la fonction interne qui n'utilise pas job_queue et permet l'envoi
		// de pièces jointes
		$envoyer = charger_fonction('envoyer_notification', 'inc');
		$envoyer($destinataires, $msg_mail, $sujet_mail, $pieces);
    }
}
?>
