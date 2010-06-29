<?php

function notifications_saveauto_save_dist($quoi, $id, $options){
	$cfg = lire_config('saveauto');
	include_spip('inc/envoyer_mail');
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
			$sujet_mail = "[".nettoyer_titre_email($GLOBALS['meta']['nom_site'])."] "._T('saveauto:erreur_mail_sujet')." "._T('saveauto:info_sql_base').$cfg['base'];
			$msg_mail = _T('saveauto:sauvegarde_erreur_mail')."\n\r"._T('saveauto:info_sql_base').$cfg['base']."\n\r"._T('saveauto:info_sql_serveur').$_SERVER['SERVER_NAME']."\n\r"._T('saveauto:info_sql_date').date('d/m/Y H:i')
						."\n\r"._T('saveauto:erreur_sauvegarde_intro')."\n\r".$options['err'];
			notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
		}
		/**
		 * La sauvegarde a apparemment fonctionné
		 */
		else{
			include_spip('inc/saveauto_fonctions');
			$tous = $cfg['destinataire_save'];
			$msg_mail = _T('saveauto:sauvegarde_ok_mail')."\n\r"._T('saveauto:info_sql_base').$cfg['base']."\n\r"._T('saveauto:info_sql_serveur').$_SERVER['SERVER_NAME']."\n\r"._T('saveauto:info_sql_date').date('d/m/Y H:i')."\n\r";
	        $sujet_mail = "[".nettoyer_titre_email($GLOBALS['meta']['nom_site'])."] "._T('saveauto:titre_saveauto')." "._T('saveauto:info_sql_base').$cfg['base'];
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