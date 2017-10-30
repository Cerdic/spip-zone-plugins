<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/mailsubscribers');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_mailsubscriber_identifier_dist(
	$id_mailsubscriber = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	return serialize(array(intval($id_mailsubscriber)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_mailsubscriber_charger_dist(
	$id_mailsubscriber = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$valeurs = formulaires_editer_objet_charger('mailsubscriber', $id_mailsubscriber, '', $lier_trad, $retour,
		$config_fonc, $row, $hidden);
	$valeurs['_listes_dispo'] = array();
	$valeurs['listes'] = array();

	$editer_email_subscription_charger = charger_fonction('charger', 'formulaires/editer_email_subscription');
	if ($valeurs['email']) {
		$v = $editer_email_subscription_charger($valeurs['email']);
		$valeurs['_listes_dispo'] = $v['_listes_dispo'];
		$valeurs['listes'] = $v['listes'];
	}
	else {
		$valeurs['_listes_dispo'] = mailsubscribers_listes();
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_mailsubscriber_verifier_dist(
	$id_mailsubscriber = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$erreurs = formulaires_editer_objet_verifier('mailsubscriber', $id_mailsubscriber, array('email'));

	if (!isset($erreurs['email'])) {
		$email = _request('email');
		// verifier que l'email est valide
		if (!email_valide($email)) {
			$erreurs['email'] = _T('info_email_invalide');
		} else {
			// verifier que l'email n'est pas deja dans la base si c'est une tentative de creation
			if (sql_countsel('spip_mailsubscribers',
					'email=' . sql_quote($email) . ' AND id_mailsubscriber!=' . intval($id_mailsubscriber)) > 0
			) {
				// si l'email existe, proposer d'éditer le compte
				if ($id = sql_getfetsel('id_mailsubscriber', 'spip_mailsubscribers', 'email=' . sql_quote($email), '', '', '0,1')) {
					$url_page_edit = generer_url_ecrire('mailsubscriber_edit', 'id_mailsubscriber=' . intval($id));
					$erreurs['email'] = _T('mailsubscriber:erreur_adresse_existante_editer', array('url'=>$url_page_edit));
				} else {
					$erreurs['email'] = _T('mailsubscriber:erreur_adresse_existante');
				}
			}
		}
		if (!isset($erreurs['email'])
			and mailsubscribers_test_email_obfusque($email)
			and _request('listes')
		) {
			$erreurs['email'] = _T('info_email_invalide');
		}
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_mailsubscriber_traiter_dist(
	$id_mailsubscriber = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	set_request('lang', _request('langue'));

	// creation : verifier qu'on retombe pas sur un email obfusque, et dans ce cas se retablir dessus
	if (!intval($id_mailsubscriber)
		AND $id = sql_getfetsel('id_mailsubscriber', 'spip_mailsubscribers',
			"email=" . sql_quote(mailsubscribers_obfusquer_email(_request('email'))))
	) {
		$id_mailsubscriber = $id;
	}

	$res = formulaires_editer_objet_traiter('mailsubscriber', $id_mailsubscriber, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	// recuperer l'id cree eventuellement
	if ($id_mailsubscriber = $res['id_mailsubscriber']) {
		$email = sql_getfetsel('email', 'spip_mailsubscribers', 'id_mailsubscriber=' . intval($id_mailsubscriber));
		if (!mailsubscribers_test_email_obfusque($email)) {
			$editer_email_subscription_traiter = charger_fonction('traiter', 'formulaires/editer_email_subscription');
			$editer_email_subscription_traiter($email);
		}
	}


	return $res;
}
