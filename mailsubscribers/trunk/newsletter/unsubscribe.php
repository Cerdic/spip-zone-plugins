<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("action/editer_objet");
include_spip('inc/mailsubscribers');
include_spip('inc/config');
include_spip('inc/autoriser');

/**
 * Desinscrire un subscriber par son email
 * si une ou plusieurs listes precisees, le subscriber est desinscrit de ces seules listes
 * si il n'en reste aucune, le statut du subscriber est suspendu
 *
 * si aucune liste precisee, le subscriber est desinscrit de toutes les listes
 *
 * @param $email
 *   champ obligatoire
 * @param array $options
 *   listes : array
 *   notify : bool
 * @return bool
 *   true si inscrit, false sinon
 */
function newsletter_unsubscribe_dist($email, $options = array()) {
	static $dejala = false;
	if ($dejala) {return false;}

	// chercher si un tel email est deja en base
	$row = sql_fetsel('*', 'spip_mailsubscribers', 'email=' . sql_quote($email));
	if ($row) {

		$set = array();
		$trace_optin = '';
		$notify = array();

		$where = array();
		$where[] = 'id_mailsubscriber=' . intval($row['id_mailsubscriber']);

		if (isset($options['listes'])
			AND is_array($options['listes'])
		) {
			$listes = array_map('mailsubscribers_normaliser_nom_liste', $options['listes']);
			$ids = sql_allfetsel('id_mailsubscribinglist', 'spip_mailsubscribinglists', sql_in('identifiant', $listes));
			$ids = array_map('reset', $ids);
			$where[] = sql_in('id_mailsubscribinglist', $ids);
		}

		// les inscriptions pas deja en refusees pour la trace
		$pas_encore = sql_allfetsel('id_mailsubscribinglist,statut', 'spip_mailsubscriptions', 'statut!=' . sql_quote('refuse') . ' AND id_segment=0 AND ' . implode(' AND ', $where));

		// on met a jour les inscriptions pour les listes demandees (ou pour toutes les listes en cours)
		// l'option remove est utilisee pour la synchro des listes : un abonnement retire lors de la synchro n'est pas conserve en refuse (car ce n'est pas une desinscription)
		if (isset($options['remove']) and $options['remove']){
			sql_delete('spip_mailsubscriptions', $where);
		}
		else {
			// on unsubscribe bien tous les segments
			sql_updateq('spip_mailsubscriptions', array('statut' => 'refuse'), $where);
		}
		$GLOBALS['mailsubscribers_recompte_inscrits'] = true;

		if ($pas_encore) {
			$changes = sql_allfetsel('id_mailsubscribinglist', 'spip_mailsubscriptions', 'statut=' . sql_quote('refuse') . ' AND id_segment=0 AND ' . implode(' AND ', $where));
			$changes = array_map('reset', $changes);
			$changes_identifiants = array();
			foreach ($pas_encore as $sub_prev){
				if (in_array($sub_prev['id_mailsubscribinglist'], $changes)){
					$identifiant = sql_getfetsel('identifiant', 'spip_mailsubscribinglists', 'id_mailsubscribinglist='.intval($sub_prev['id_mailsubscribinglist']));
					$changes_identifiants[] = $identifiant;
					$notify[] = array(
						'identifiant' => $identifiant,
						'id_mailsubscribinglist' => $sub_prev['id_mailsubscribinglist'],
						'statut' => 'refuse',
						'statut_ancien' => $sub_prev['statut'],
					);
				}
			}
			if ($changes_identifiants) {
				$trace_optin .= '[' . implode(',', $changes_identifiants) . ':' . _T('mailsubscriber:info_statut_refuse') . '] ';
			}
		}

		// on regarde les inscriptions en cours : si aucune prop ou valide, l'abonne passe en refuse, mail obfusque
		$encore = sql_countsel('spip_mailsubscriptions', 'id_mailsubscriber=' . intval($row['id_mailsubscriber']) . ' AND ' . sql_in('statut', array('prop', 'valide')));
		if (!$encore) {
			if (!in_array($row['statut'], array('refuse', 'poubelle'))) {
				$set['statut'] = "refuse";
			}
			// pris en charge par pipeline + cron
			//$set['email'] = mailsubscribers_obfusquer_email($email);
		}
		if ($trace_optin) {
			$set['optin'] = mailsubscribers_trace_optin($trace_optin,
				sql_getfetsel('optin', 'spip_mailsubscribers', 'id_mailsubscriber=' . intval($row['id_mailsubscriber'])));
		}

		if (count($set)) {
			$dejala = true; // ne pas accepter la reentrance de validation des inscriptions quand on valide l'inscrit
			autoriser_exception("modifier", "mailsubscriber", $row['id_mailsubscriber']);
			autoriser_exception("instituer", "mailsubscriber", $row['id_mailsubscriber']);
			autoriser_exception("superinstituer", "mailsubscriber", $row['id_mailsubscriber']);
			// d'abord le statut pour notifier avec le bon mail, sauf si notify=false en option
			if (isset($set['statut'])
				AND (!isset($options['notify']) OR $options['notify'])
			) {
				objet_modifier("mailsubscriber", $row['id_mailsubscriber'], array('statut' => $set['statut']));
				unset($set['statut']);
			}
			// ensuite l'email ou autre si besoin
			if (count($set)) {
				objet_modifier("mailsubscriber", $row['id_mailsubscriber'], $set);
			}
			autoriser_exception("modifier", "mailsubscriber", $row['id_mailsubscriber'], false);
			autoriser_exception("instituer", "mailsubscriber", $row['id_mailsubscriber'], false);
			autoriser_exception("superinstituer", "mailsubscriber", $row['id_mailsubscriber'], false);
			$dejala = false;

			// actualiser les segments en auto_update
			include_spip('inc/mailsubscribinglists');
			$unsubscribed_lists = array();
			if ($notify) {
				$unsubscribed_lists = array_column($notify, 'id_mailsubscribinglist');
			}
			mailsubscribers_actualise_segments($row['id_mailsubscriber'], $unsubscribed_lists);
		}

		// notifier
		if ($notify and (!isset($options['notify']) or $options['notify'])){
			$notifications = charger_fonction('notifications','inc');
			$notifications_options = array('subscriptions' => $notify);
			$notifications('instituermailsubscriptions', $row['id_mailsubscriber'], $notifications_options);
		}

	}

	return true;
}
