<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Notification lors de la vérification de présence des binaires
 * s'il en manque
 *
 * @param unknown_type $quoi
 * @param unknown_type $id
 * @param unknown_type $options
 */
function notifications_spipmotion_verifier_binaires($quoi, $id, $options){
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
				'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options)
			,
				'data'=>$tous)
		);
		if(in_array('exec',$options['erreurs'])){
			unset($erreurs[0]);
			$msg_mail = recuperer_fond('notifications/spipmotion_verifier_binaires',array('exec'=>'oui','nb' => 1));
		}else{
			$msg_mail = recuperer_fond('notifications/spipmotion_verifier_binaires',array('erreurs'=>$options['erreurs'],'nb' => $nb));
		}
		/**
		 * Nettoyage de la liste d'emails en vérifiant les doublons
		 * et la validité des emails
		 */
		notifications_nettoyer_emails($destinataires);
		notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
	}
}
?>