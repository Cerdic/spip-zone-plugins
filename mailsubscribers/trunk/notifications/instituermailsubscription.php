<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// Fonction appelee par divers pipelines
// https://code.spip.net/@notifications_instituerarticle_dist
function notifications_instituermailsubscription_dist($quoi, $id_mailsubscriber, $options) {

	// ne devrait jamais se produire
	if (isset($options['statut'])
		and ($options['statut'] == $options['statut_ancien'])
	){
		spip_log("instituermailsubscription #$id_mailsubscriber : statut inchange", 'notifications'._LOG_INFO_IMPORTANTE);
		return;
	}

	// @deprecated : il faut normalement utiliser l'option notify de subscribe et unsubscribe
	// pour eviter l'envoi des notifications d'inscription/desincription
	if (isset($GLOBALS['notification_instituermailsubscriber_status']) AND !$GLOBALS['notification_instituermailsubscriber_status'])
		return;


	if (!isset($options['id_mailsubscribinglist'])){
		spip_log("instituermailsubscription #$id_mailsubscriber : id_mailsubscribinglist inconnu", 'notifications'._LOG_INFO_IMPORTANTE);
	}

	include_spip('inc/texte');

	$modele = "";
	if (isset($options['statut'])
		and $options['statut'] == 'valide'
	) {
		$modele = "notifications/mailsubscriber_subscribe";
	} elseif (isset($options['statut'])
		and $options['statut_ancien'] == 'valide'
	) {
		$modele = "notifications/mailsubscriber_unsubscribe";
	} elseif (isset($options['statut'])
		and $options['statut'] == 'prop'
	) {
		if (isset($options['invite_email_from']) AND strlen($options['invite_email_from'])) {
			$modele = "notifications/mailsubscriber_invite_confirm";
		} else {
			$modele = "notifications/mailsubscriber_confirm";
		}
	}
	if ($modele) {
		$destinataires = sql_allfetsel("email", "spip_mailsubscribers", "id_mailsubscriber=" . intval($id_mailsubscriber));
		$destinataires = array_map('reset', $destinataires);

		$destinataires = pipeline('notifications_destinataires',
			array(
				'args' => array('quoi' => $quoi, 'id' => $id_mailsubscriber, 'options' => $options)
			,
				'data' => $destinataires
			)
		);

		// precaution : enlever les adresses en "@example.org"
		foreach ($destinataires as $k => $email) {
			if (preg_match(",@example.org$,i", $email)) {
				unset($destinataires[$k]);
			}
		}

		if (count($destinataires)) {
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc'); // pour nettoyer_titre_email
			$env = $options;
			unset($env['statut']);
			$env['id_mailsubscriber'] = $id_mailsubscriber;
			$texte = recuperer_fond($modele, $env);
			notifications_envoyer_mails($destinataires, $texte);
		}
	}
}
