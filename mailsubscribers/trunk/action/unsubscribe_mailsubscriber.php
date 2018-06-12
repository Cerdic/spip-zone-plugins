<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Des-inscrire un email deja en base
 * (mise a jour du statut en refuse)
 *
 * @param string $email
 * @param string $identifiant
 * @param bool $double_optin
 */
function action_unsubscribe_mailsubscriber_dist($email = null, $identifiant = null, $double_optin = true) {
	include_spip('mailsubscribers_fonctions');
	include_spip('inc/mailsubscribers');

	if (is_null($email)) {
		$arg = mailsubscribers_verifier_args_action('unsubscribe');
		if ($arg){
			list($email, $identifiant) = $arg;
		}
	} else {
		$double_optin = false;
	}

	$subscriber = charger_fonction('subscriber','newsletter');
	if (!$email or !$infos = $subscriber($email)) {
		include_spip('inc/minipres');
		echo minipres(_T('info_email_invalide') . '<br />' . entites_html($email));
		exit;
	}

	$titre_liste = '';
	$status = $infos['status'];
	if ($identifiant){
		$liste = sql_fetsel('id_mailsubscribinglist, titre_public', 'spip_mailsubscribinglists', 'identifiant=' . sql_quote($identifiant));
		if ($liste['titre_public']) {
			include_spip('inc/texte');
			$titre_liste = supprimer_numero(typo($liste['titre_public']));
		}
		else {
			$titre_liste = '#' . $liste['id_mailsubscribinglist'];
		}
	}

	if ($status !== 'on') {
		$titre = _T('mailsubscriber:unsubscribe_deja_texte', array('email' => $email));
	}
	else {
		$unsubscribe = charger_fonction('unsubscribe','newsletter');
		$options = array();

		$env = array(
			'email' => "<b>$email</b>",
			'titre_liste' => $titre_liste,
			'nom_site_spip' => $GLOBALS['meta']['nom_site'],
			'url_site_spip' => $GLOBALS['meta']['adresse_site']
		);
		if ($double_optin) {
			include_spip('inc/filtres');
			if ($titre_liste) {
				$titre = _T('mailsubscriber:unsubscribe_texte_confirmer_email_liste_1', $env);
				// bouton de desinscription a cette liste si on y est abonne ET si plusieurs abonnements
				if (isset($infos['subscriptions'][$identifiant]['status'])
				  and $infos['subscriptions'][$identifiant]['status']=='on'
				  and isset($infos['listes'])
				  and count($infos['listes'])>1) {
					$titre .= "<br /><br />" . bouton_action(_T('newsletter:bouton_unsubscribe'),
							generer_action_auteur('confirm_unsubscribe_mailsubscriber',
								mailsubscriber_base64url_encode($email . ":$identifiant:".time())));

				}
			} else {
				$titre = _T('mailsubscriber:unsubscribe_texte_confirmer_email_1', $env);
			}

			// bouton de desinscription globale
			$titre .= "<br /><br />" . bouton_action(_T('newsletter:bouton_unsubscribe_all'),
					generer_action_auteur('confirm_unsubscribe_mailsubscriber',
						mailsubscriber_base64url_encode($email . "::".time())));
		}
		else {
			$options['force'] = true;
			if ($titre_liste) {
				$titre = _T('mailsubscriber:unsubscribe_texte_email_liste_1', $env);
			} else {
				$titre = _T('mailsubscriber:unsubscribe_texte_email_1', $env);
			}
			if ($identifiant){
				$options['listes'] = array($identifiant);
			}
			$unsubscribe($email, $options);
		}

	}

	// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	include_spip('inc/minipres');
	echo minipres($titre, "<style>h1{font-weight: normal}</style>", "", true);

}

function mailsubscriber_base64url_encode($data) {
	return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}