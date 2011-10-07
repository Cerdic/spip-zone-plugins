<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_mes_fichiers_sauver_dist($quoi, $id, $options){
	$cfg = lire_config('mes_fichiers');
	if (($cfg['notif_active'] == 'oui') && !$options['err']) {
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
		if (intval($options['auteur'])) {
			$auteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur='.intval($options['auteur']));
		}else{
			$auteur = $options['auteur'];
		}
		$sujet_mail = "[".typo($GLOBALS['meta']['nom_site'])."] "._T('mes_fichiers:message_sauver_sujet');
		$msg_mail = _T('mes_fichiers:message_notif_sauver_intro',array('auteur' => $auteur));
		notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
    }
}
?>