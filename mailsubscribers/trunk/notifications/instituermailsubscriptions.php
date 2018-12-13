<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// Notifier la modification de statut d'une ou plusieurs inscriptions a des listes (d'un meme subscriber)
// etend l'ancienne instituermailsubscription
function notifications_instituermailsubscriptions_dist($quoi, $id_mailsubscriber, $options) {

	// @deprecated : il faut normalement utiliser l'option notify de subscribe et unsubscribe
	// pour eviter l'envoi des notifications d'inscription/desincription
	if (isset($GLOBALS['notification_instituermailsubscriber_status']) AND !$GLOBALS['notification_instituermailsubscriber_status'])
		return;

	$envois = array();
	include_spip('inc/texte');

	if (isset($options['subscriptions']) and count($options['subscriptions'])) {

		foreach ($options['subscriptions'] as $subscription) {

			if (!isset($subscription['id_mailsubscribinglist'])){
				spip_log("instituermailsubscriptions #$id_mailsubscriber : id_mailsubscribinglist inconnu " . json_encode($subscription), 'notifications'._LOG_INFO_IMPORTANTE);
				continue; // rien d'autre a faire pour cette subscription
			}
			$id_mailsubscribinglist = $subscription['id_mailsubscribinglist'];

			// ne devrait jamais se produire
			if (isset($subscription['statut'])
				and ($subscription['statut'] == $subscription['statut_ancien'])
			){
				spip_log("instituermailsubscriptions #$id_mailsubscriber a liste #$id_mailsubscribinglist : statut inchange", 'notifications'._LOG_INFO_IMPORTANTE);
				continue; // rien d'autre a faire pour cette subscription
			}

			// trouver le modele d'envoi
			$modele = "";
			if (isset($subscription['statut'])
				and $subscription['statut'] == 'valide'
			) {
				$modele = "notifications/mailsubscriber_subscribe";
			} elseif (isset($subscription['statut_ancien'])
				and $subscription['statut_ancien'] == 'valide'
			) {
				$modele = "notifications/mailsubscriber_unsubscribe";
			} elseif (isset($subscription['statut'])
				and $subscription['statut'] == 'prop'
			) {
				if (isset($subscription['invite_email_from']) AND strlen($subscription['invite_email_from'])) {
					$modele = "notifications/mailsubscriber_invite_confirm";
				} else {
					$modele = "notifications/mailsubscriber_confirm";
				}
			}

			if ($modele){

				if (!isset($envois[$modele])) {
					$envois[$modele] = array(
						'id_mailsubscribinglists' => array(),
						'contexte' => array(),
					);
				}

				$envois[$modele]['id_mailsubscribinglists'][] = $id_mailsubscribinglist;
				$envois[$modele]['contexte'][$id_mailsubscribinglist] = $subscription;
			}


		}

	}

	spip_log("instituermailsubscriptions #$id_mailsubscriber : " . count($envois) . ' mails differents a envoyer', 'notifications');

	if ($envois) {
		$contexte = $options;
		unset($contexte['subscriptions']);
		$contexte['id_mailsubscriber'] = $id_mailsubscriber;

		$destinataires = sql_getfetsel("email", "spip_mailsubscribers", "id_mailsubscriber=" . intval($id_mailsubscriber));
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

			foreach ($envois as $modele => $envoi) {

				if (count($envoi['id_mailsubscribinglists'])>1
				  and $modele_multiples = "$modele-multiples"
					and trouver_fond($modele_multiples)) {

					spip_log("instituermailsubscriptions #$id_mailsubscriber : $modele_multiples : envoi en un seul mail pour listes #" . implode(', #', $envoi['id_mailsubscribinglists']), 'notifications' . _LOG_INFO_IMPORTANTE);

					$env = array();
					while (count($envoi['contexte'])) {
						$env = array_merge($env, array_shift($envoi['contexte']));
					}
					$env = array_merge($env, $contexte);
					$env['id_mailsubscribinglists'] = $envoi['id_mailsubscribinglists'];
					unset($env['statut']);

					$texte = recuperer_fond($modele_multiples, $env);
					notifications_envoyer_mails($destinataires, $texte);

				}
				else {

					foreach ($envoi['id_mailsubscribinglists'] as $id_mailsubscribinglist) {

						spip_log("instituermailsubscriptions #$id_mailsubscriber : $modele : envoi mail pour liste #$id_mailsubscribinglist", 'notifications' . _LOG_INFO_IMPORTANTE);

						$env = array_merge($envoi['contexte'][$id_mailsubscribinglist], $contexte);
						unset($env['statut']);
						$env['id_mailsubscribinglist'] = $id_mailsubscribinglist;

						$texte = recuperer_fond($modele, $env);
						notifications_envoyer_mails($destinataires, $texte);

					}

				}

			}
		}
		else {
			spip_log("instituermailsubscriptions #$id_mailsubscriber : aucun destinataire - rien a faire", 'notifications' . _LOG_INFO_IMPORTANTE);
		}
	}

}
