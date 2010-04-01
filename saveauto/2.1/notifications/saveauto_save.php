<?php

function notifications_saveauto_save_dist($quoi, $id, $options){
	$cfg = lire_config('saveauto');
	if (!empty($cfg['destinataire_save']) OR $options['err']) {
		/**
		 * Notifier une erreur dans la génération d'une sauvegarde
		 */
		if($options['err']){
			$tous = $cfg['destinataire_save'] ? $cfg['destinataire_save'] : $GLOBALS['meta']['email_webmaster'];
			$destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
				,
					'data'=>$tous)
			);
			$sujet_mail = "[".typo($GLOBALS['meta']['nom_site'])."] "._T('saveauto:erreur_mail_sujet_')." "._T('saveauto:base').$cfg['base'];
			$msg_mail = _T('saveauto:sauvegarde_erreur_mail')."\n\r"._T('saveauto:base').$cfg['base']."\n\r"._T('saveauto:serveur').$_SERVER['SERVER_NAME']."\n\r"._T('saveauto:date').date('d/m/Y H:i')
						."\n\r"._T('saveauto:erreur_sauvegarde_intro')."\n\r".$options['err'];
			notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
		}
		/**
		 * La sauvegarde a apparemment fonctionné
		 */
		else{
			include_spip('inc/saveauto_fonctions');
			$tous = $cfg['destinataire_save'];
			$msg_mail = _T('saveauto:sauvegarde_ok_mail')."\n\r"._T('saveauto:base').$cfg['base']."\n\r"._T('saveauto:serveur').$_SERVER['SERVER_NAME']."\n\r"._T('saveauto:date').date('d/m/Y H:i');
	        $sujet_mail = "[".typo($GLOBALS['meta']['nom_site'])."] "._T('saveauto:saveauto')." "._T('saveauto:base').$cfg['base'];
	        $destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
				,
					'data'=>$tous)
			);
	        saveauto_mail_attachement($destinataires, $sujet_mail, $msg_mail, $options['chemin_fichier'], $options['nom_fichier']);
		}
    }
}
?>