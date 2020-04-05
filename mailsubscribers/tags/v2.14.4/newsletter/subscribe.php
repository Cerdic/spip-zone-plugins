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
include_spip('inc/filtres');
include_spip('inc/autoriser');

/**
 * Inscrit un subscriber par son email
 * si le subscriber existe deja, on met a jour les informations (nom, listes, lang)
 * l'ajout d'une inscription a une liste est cumulatif : si on appelle plusieurs fois la fonction avec le meme email
 * et plusieurs listes differentes, l'inscrit sera sur chaque liste
 * Pour retirer une liste il faut desinscrire
 *
 * Quand aucune liste n'est indiquee :
 *   si l'email n'est inscrit a rien, on l'inscrit a la liste generale 'newsletter'
 *   si l'email est deja inscrit, on modifie ses informations (nom, lang) on ne change pas ses inscriptions sauf si force=true,
 *     dans ce cas on valide les inscriptions en attente
 *
 * @param $email
 *   champ obligatoire
 * @param array $options
 *   nom : string
 *   listes : array (si non fourni, inscrit a la liste generale 'newsletter')
 *   lang : string
 *   force : bool|int true permet de forcer une inscription sans doubleoptin (passe direct en valide), -1 permet de forcer le doubleoptin
 *   graceful : bool permet a contrario de ne pas inscrire quelqu'un qui s'est desabonne (utilise lors de l'import en nombre, l'utilisateur est ignore dans ce cas)
 *   notify : bool
 *   invite_email_from : text . utilisé par le formulaire #NEWSLETTER_INVITE, permet de renseigner la personne qui invite à s'inscrire à la newsletter
 *   invite_email_text : text . utilisé par le formulaire #NEWSLETTER_INVITE, permet de renseigner le message personnalisé d'invitation
 * @return bool
 *   true si inscrit comme demande, false sinon
 */
function newsletter_subscribe_dist($email, $options = array()) {
	static $dejala = false;
	if ($dejala) {return false;}

	if (!$email = trim($email)) {
		return false;
	}
	// on abonne pas un email invalide ou obfusque !
	if (!$email = email_valide($email)
		or mailsubscribers_test_email_obfusque($email)) {
		spip_log("email invalide pour abonnement : $email", "mailsubscribers." . _LOG_INFO_IMPORTANTE);

		return false;
	}

	$set = array();
	$listes = false;
	$trace_optin = '';
	foreach (array('lang', 'nom', 'invite_email_from', 'invite_email_text') as $k) {
		if (isset($options[$k])) {
			$set[$k] = $options[$k];
		}
	}

	if (isset($options['listes'])
		AND is_array($options['listes'])
	) {
		$listes = array_map('mailsubscribers_normaliser_nom_liste', $options['listes']);
	}
	if (!is_array($listes)) {
		$listes = array(mailsubscribers_normaliser_nom_liste());
	}

	// chercher si un tel email est deja en base
	$row = sql_fetsel('*', 'spip_mailsubscribers',
		'email=' . sql_quote($email) . " OR email=" . sql_quote(mailsubscribers_obfusquer_email($email)));

	// Si c'est une creation d'inscrit
	if (!$row) {
		if (isset($options['invite_email_from']) AND strlen($options['invite_email_from'])) {
			spip_log("Invitation " . $options['invite_email_from'] . " invite $email a s'inscrire ", "mailsubscribers." . _LOG_INFO_IMPORTANTE);
		} else {
			spip_log("Inscription liste $email ", "mailsubscribers." . _LOG_INFO_IMPORTANTE);
		}
		// email unique
		$set['email'] = $email;
		if (!isset($set['lang'])) {
			$set['lang'] = $GLOBALS['meta']['langue_site'];
		}
		// date par defaut
		$set['statut'] = 'prepa';
		$set['date'] = date('Y-m-d H:i:s');

		if ($id = objet_inserer("mailsubscriber", 0, $set)) {
			$row = sql_fetsel('*', 'spip_mailsubscribers', 'id_mailsubscriber=' . intval($id));
			// test de securite car $set pas forcement pris en charge dans objet_inserer
			if ($row['email'] !== $set['email']) {
				autoriser_exception("modifier", "mailsubscriber", $row['id_mailsubscriber']);
				autoriser_exception("instituer", "mailsubscriber", $row['id_mailsubscriber']);
				objet_modifier("mailsubscriber", $row['id_mailsubscriber'], $set);
				autoriser_exception("modifier", "mailsubscriber", $row['id_mailsubscriber'], false);
				autoriser_exception("instituer", "mailsubscriber", $row['id_mailsubscriber'], false);
				$row = sql_fetsel('*', 'spip_mailsubscribers', 'id_mailsubscriber=' . intval($id));
			}
			$set = array();
		} else {
			spip_log("Impossible de creer un mailsubscriber : " . var_export($set, true), "mailsubscribers." . _LOG_ERREUR);

			return false;
		}
	}

	// proceder aux inscriptions
	// statut d'inscription en prop (doubleoptin) ou valide (simpleoptin)
	$statut_defaut = 'prop';
	$notify = array();
	if (
		(isset($options['force']) AND $options['force'] === true)
		OR !lire_config('mailsubscribers/double_optin', 0)
	) {
		if (!isset($options['force']) or $options['force'] !== -1) {
			$statut_defaut = 'valide';
		}
	}
	if ($listes
		and $id_mailsubscriber = $row['id_mailsubscriber']
	) {
		foreach ($listes as $identifiant) {
			if ($id_mailsubscribinglist = sql_getfetsel('id_mailsubscribinglist', 'spip_mailsubscribinglists',
				'identifiant=' . sql_quote($identifiant))
			) {
				$sub_prev = $sub = sql_fetsel('*', 'spip_mailsubscriptions',
					'id_mailsubscriber=' . intval($id_mailsubscriber) . ' AND id_segment=0 AND id_mailsubscribinglist=' . intval($id_mailsubscribinglist));
				$ins = array(
					'id_mailsubscriber' => $id_mailsubscriber,
					'id_mailsubscribinglist' => $id_mailsubscribinglist,
					'statut' => $statut_defaut,
				);
				if (!$sub_prev) {
					sql_insertq('spip_mailsubscriptions', $ins);
					// on verifie l'inscription, en cas de concurrence
					$sub = sql_fetsel('*', 'spip_mailsubscriptions',
						'id_mailsubscriber=' . intval($id_mailsubscriber) . ' AND id_segment=0 AND id_mailsubscribinglist=' . intval($id_mailsubscribinglist));
				}
				// le statut doit etre celui qu'on a voulu mettre - ou mieux : deja valide
				if ($sub['statut'] !== $ins['statut'] and $sub['statut'] !== 'valide') {
					// si c'est graceful on ne reinscrit pas quelqu'un qui s'est desinscrit
					if (!isset($options['graceful']) or $options['graceful'] !== true) {
						sql_updateq('spip_mailsubscriptions', $ins,
							'id_mailsubscriber=' . intval($id_mailsubscriber) . ' AND id_mailsubscribinglist=' . intval($id_mailsubscribinglist));
						$sub['statut'] = $ins['statut'];
					}
				}
				// une adresse en prepa reste en prepa tant qu'un email n'a pas ete valide
				if ($sub['statut'] == 'prop'
					and (!isset($set['statut']) or !in_array($set['statut'], array('prepa', 'valide')))
					and !in_array($row['statut'], array('prepa', 'valide'))
				) {
					$set['statut'] = 'prop';
					$set['email'] = $email; // si email obfusque
				} elseif ($sub['statut'] == 'valide') {
					$set['statut'] = 'valide';
					$set['email'] = $email; // si email obfusque
				}
				if (!$sub_prev or $sub['statut'] !== $sub_prev['statut']) {
					$trace_optin .= '[' . $identifiant . ':' . _T('mailsubscriber:info_statut_' . $sub['statut']) . '] ';
				}
				$notify[] = array(
					'identifiant' => $identifiant,
					'id_mailsubscribinglist' => $id_mailsubscribinglist,
					'statut' => $sub['statut'],
					'statut_ancien' => (isset($sub_prev['statut'])?$sub_prev['statut']:'prepa'),
				);
				$GLOBALS['mailsubscribers_recompte_inscrits'] = true;
			}
		}
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
		objet_modifier("mailsubscriber", $row['id_mailsubscriber'], $set);
		autoriser_exception("modifier", "mailsubscriber", $row['id_mailsubscriber'], false);
		autoriser_exception("instituer", "mailsubscriber", $row['id_mailsubscriber'], false);
		autoriser_exception("superinstituer", "mailsubscriber", $row['id_mailsubscriber'], false);
		$dejala = false;
	}

	// actualiser les segments en auto_update
	include_spip('inc/mailsubscribinglists');
	mailsubscribers_actualise_segments($row['id_mailsubscriber']);

	// notifier
	if ($notify and (!isset($options['notify']) or $options['notify'])){
		$notifications = charger_fonction('notifications','inc');
		foreach ($notify as $k => $option){
			if (isset($options['invite_email_from']) AND strlen($options['invite_email_from'])) {
				$notify[$k]['invite_email_from'] = $options['invite_email_from'];
				if (isset($options['invite_email_text'])){
					$notify[$k]['invite_email_text'] = $notify[$k]['invite_email_text'];
				}
			}
		}
		$notifications_options = array('subscriptions' => $notify);
		$notifications('instituermailsubscriptions', $row['id_mailsubscriber'], $notifications_options);
	}
	return true;
}
