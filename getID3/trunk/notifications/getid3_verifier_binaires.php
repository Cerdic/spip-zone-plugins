<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Notification lors de la vérification de présence des binaires
 * s'il en manque
 *
 * @param unknown_type $quoi
 * @param unknown_type $id
 * @param unknown_type $options
 */
function notifications_getid3_verifier_binaires($quoi, $id, $options){
	if(!defined('_GETID3_NO_NOTIFICATIONS')){
		include_spip('inc/envoyer_mail'); #pour nettoyer_titre_emails
		if(($nb = count($options['erreurs'])) > 0){
	
			$tous = array();
			$result = sql_select("email","spip_auteurs","webmestre='oui'");
	
			while ($qui = sql_fetch($result)) {
				if ($qui['email'])
					$tous[] = $qui['email'];
			}
			$destinataires = pipeline('notifications_destinataires',
				array(
					'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options),
					'data'=>$tous)
			);
			$msg_mail = recuperer_fond('notifications/getid3_verifier_binaires',array('erreurs'=>$options['erreurs'],'nb' => $nb));
			/**
			 * Nettoyage de la liste d'emails en vérifiant les doublons
			 * et la validité des emails
			 */
			notifications_nettoyer_emails($destinataires);
			foreach ($destinataires as $email) {
				notifications_envoyer_mails($email, $msg_mail);
			}
		}
	}
}
?>