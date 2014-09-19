<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_preparer_destinataires($quoi, $id, $options) {
	include_spip('inc/config');

	// Recuperation des destinataires configurés
	$mails = lire_config('saveauto/notif_mail');
	$tous = ($mails) ? explode(',', $mails) : array();
	if (lire_config('saveauto/notification_webmestre') == 1)
		$tous[] = $GLOBALS['meta']['email_webmaster'];
	$destinataires = pipeline('notifications_destinataires',
		array(
			'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options),
			'data'=>$tous)
	);

	 // Nettoyage de la liste d'emails en vérifiant les doublons
	 // et la validité des emails
	notifications_nettoyer_emails($destinataires);

	return $destinataires;
}

?>
