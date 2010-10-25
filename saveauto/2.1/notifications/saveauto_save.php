<?php

function notifications_saveauto_save_dist($quoi, $id, $options){
	$cfg = lire_config('saveauto');
	include_spip('inc/envoyer_mail');
	if(defined('_DIR_SITE')){
		$racine = _DIR_SITE;
	}else{
		$racine = _DIR_RACINE;
	}
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
			$tous = $cfg['destinataire_save'];
			$msg_mail = _T('saveauto:sauvegarde_ok_mail')."\n\r"._T('saveauto:info_sql_base').$cfg['base']."\n\r"._T('saveauto:info_sql_serveur').$_SERVER['SERVER_NAME']."\n\r"._T('saveauto:info_sql_date').date('d/m/Y H:i')."\n\r";
	        $sujet_mail = "[".nettoyer_titre_email($GLOBALS['meta']['nom_site'])."] "._T('saveauto:titre_saveauto')." "._T('saveauto:info_sql_base').$cfg['base'];
	        $destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
				,
					'data'=>$tous)
			);
			if((filesize($options['chemin_fichier']) / 1000000) < lire_config('saveauto/mail_max_size','2')){
				$corps['texte'] = $msg_mail;
				$corps['pieces_jointes'][0] = array(
					'chemin'=>$options['chemin_fichier'],
					'nom'=>basename($options['chemin_fichier']),
					'encodage' => 'base64',
					'mime' => ''
				);
			}else{
				$corps = $msg_mail;
				$fichier = str_replace($racine,'',$options['chemin_fichier']);
				$corps .= _T('saveauto:erreur_mail_fichier_lourd',array('fichier'=>$fichier));
			}
			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			$envoyer_mail($destinataires, $sujet_mail, $corps, $from = "", $headers = "");
		}
    }
}
?>