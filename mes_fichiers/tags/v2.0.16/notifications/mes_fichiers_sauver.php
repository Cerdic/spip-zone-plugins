<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_mes_fichiers_sauver_dist($quoi, $id, $options){
	include_spip('inc/config');
	$notif_active = (lire_config('mes_fichiers/notif_active', 'non') == 'oui');

	if ($notif_active
	AND !$options['err']) {
		// preparation de la liste des destinataires
		include_spip('inc/mes_fichiers_utils');
		$destinataires = mes_fichiers_preparer_destinataires($quoi, $id, $options);

		// Determination de l'auteur de la sauvegarde
		if (intval($options['auteur'])) {
			$auteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur='.intval($options['auteur']));
		}else{
			$auteur = $options['auteur'];
		}

		// Construction du sujet du mail
		include_spip('inc/texte');
		$sujet_mail = "[" . typo($GLOBALS['meta']['nom_site'])
					. "][mes_fichiers] "
					. _T('mes_fichiers:message_sauver_sujet');

		// Construction du texte du mail
		$msg_mail = _T('mes_fichiers:message_notif_sauver_intro', array('auteur' => $auteur));

		// Envoi de la notification
		notifications_envoyer_mails($destinataires, $msg_mail, $sujet_mail);
    }
}
?>