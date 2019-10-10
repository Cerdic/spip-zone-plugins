<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fusionne le cas subscriber obfusque + subscriber non obfusque
 *
 * @param string $email
 * @param array $subscribers
 * @return bool|array
 */
function action_mailsubscribers_fusionner_doublons_dist($email, $subscribers) {

	$id_mailsubscriber_main = 0;
	$subscriptions_old = [];
	$subscribers_to_remove = [];
	$infos_defaut = [];
	foreach ($subscribers as $subscriber) {
		if ($subscriber['email'] === $email) {
			if ($id_mailsubscriber_main) {
				// AMBIGUITE : plusieurs subscribers avec le bon email, c'est normalement impossible sauf defaut d'unicite en base
				// on ne fait rien et on renvoie le premier trouve
				spip_log("Fusion subscribers email=$email AMBIGUITE - on ne fait rien, reparer la base", "mailsubscriber_fusionner_doublons" . _LOG_CRITIQUE);
				return $infos_defaut;
			}
			$id_mailsubscriber_main = $subscriber['id_mailsubscriber'];
			$infos_defaut = $subscriber;
		}
		else {
			$subscribers_to_remove[] = $subscriber['id_mailsubscriber'];
			$subscriptions = sql_allfetsel("*", "spip_mailsubscriptions", "id_mailsubscriber=".intval($subscriber['id_mailsubscriber']));
			foreach ($subscriptions as $subscription) {
				$subscriptions_old[] = $subscription;
			}
		}
	}

	if (!$id_mailsubscriber_main) {
		return false;
	}

	#var_dump($id_mailsubscriber_main, $subscriptions_old, $subscribers_to_remove);
	if (count($subscriptions_old)) {
		foreach ($subscriptions_old as $subscription) {
			$subscription['id_mailsubscriber'] = $id_mailsubscriber_main;
			// on essaye de reinserer ces lignes avec le bon subscriber, a l'aveugle
			// tant pis si echec
			sql_insertq("spip_mailsubscriptions",$subscription);
		}
	}
	if (count($subscribers_to_remove)) {
		spip_log("Subscribers ".implode(',', $subscribers_to_remove). " POUBELLE, doublons de #$id_mailsubscriber_main ($email)", "mailsubscriber_fusionner_doublons" . _LOG_INFO_IMPORTANTE);
		sql_updateq("spip_mailsubscribers", array("statut" => "poubelle"), sql_in('id_mailsubscriber', $subscribers_to_remove));
	}

	return $infos_defaut;
}