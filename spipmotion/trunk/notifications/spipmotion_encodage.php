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
 * Notification à la fin de l'encodage
 *
 * @param unknown_type $quoi
 * @param unknown_type $id
 * @param unknown_type $options
 */
function notifications_spipmotion_encodage_dist($quoi, $id, $options){
	spip_log('notif encodage','spipmotion');
	include_spip('inc/config');
	$en_cours = sql_countsel('spip_facd_conversions','id_document = '.intval($options['source']['id_document']).' AND statut IN ("non","en_cours","erreur")');
	$infos_encodage = sql_fetsel('*','spip_facd_conversions','id_facd_conversion ='.intval($id));

	$options['encodage_restant'] = $en_cours;

	if(lire_config('spipmotion/debug_mode') == 'on'){
		/**
		 * Il reste des versions à encoder
		 * On ne notifie que le webmestre si spipmotion est en mode debug
		 * On lui envoie le log également si possible
		 */
		$infos_encodage = sql_fetsel('*','spip_facd_conversions','id_facd_conversion ='.intval($id));
		$options['encodage_statut'] = $infos_encodage['statut'];

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
		$msg_mail = recuperer_fond('notifications/spipmotion_encodage_webmestre',array('id_facd_conversion'=>$id,'fichier_log'=>$options['fichier_log']));

		/**
		 * Nettoyage de la liste d'emails en vérifiant les doublons
		 * et la validité des emails
		 */
		notifications_nettoyer_emails($destinataires);

		notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
	}
	if($en_cours == 0){
		$msg_mail = recuperer_fond('notifications/spipmotion_encodage_termine',array('id_facd_conversion'=>$id));

		$tous = array();
		$tous[] = sql_getfetsel('email','spip_auteurs','id_auteur='.intval($infos_encodage['id_auteur']));
		$webmestres = sql_select("email","spip_auteurs","webmestre='oui'");

		while ($qui = sql_fetch($webmestres)) {
			if ($qui['email'])
				$tous[] = $qui['email'];
		}

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

		notifications_envoyer_mails($destinataires, $msg_mail,$sujet_mail);
	}
}
?>