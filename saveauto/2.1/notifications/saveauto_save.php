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
		$serveur = $_SERVER['SERVER_NAME'];
		if($options['err']){
			$tous = $cfg['destinataire_save'] ? $cfg['destinataire_save'] : $GLOBALS['meta']['email_webmaster'];
			$destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
				,
					'data'=>$tous)
			);
			$sujet_mail = "[".nettoyer_titre_email($GLOBALS['meta']['nom_site'])."] "._T('saveauto:erreur_mail_sujet')." "._T('saveauto:info_sql_base').$cfg['base'];
			$msg_mail = recuperer_fond('notifications/saveauto_save_pas_ok',array('cfg'=>$cfg,'erreur'=>$options['err'],'serveur'=>$serveur));
			notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
		}
		/**
		 * La sauvegarde a apparemment fonctionné
		 */
		else{
			$filesize = (filesize($options['chemin_fichier']) / 1000000);
			$fichier = str_replace($racine,'',$options['chemin_fichier']);
			
			$tous = $cfg['destinataire_save'];
			
	        $sujet_mail = "[".nettoyer_titre_email($GLOBALS['meta']['nom_site'])."] "._T('saveauto:titre_saveauto')." "._T('saveauto:info_sql_base').$cfg['base'];
	        $msg_mail = recuperer_fond('notifications/saveauto_save_ok',array('cfg'=>$cfg,'filesize'=>$filesize,'serveur'=>$serveur,'fichier_racine'=>$fichier));
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
				
				//$corps .= _T('saveauto:erreur_mail_fichier_lourd',array('fichier'=>$fichier));
			}
			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			$envoyer_mail($destinataires, $sujet_mail, $corps, $from = "", $headers = "");
		}
    }
}
?>