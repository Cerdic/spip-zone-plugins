<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Inscrire un email a une liste (inscription deja en base)
 * (mise a jour du statut en prop ou valide selon l'option double-optin)
 *
 * @param string $email
 * @param string $identifiant
 * @param null|bool $double_optin
 */
function action_subscribe_mailsubscriber_dist($email = null, $identifiant = null, $double_optin = null) {
	include_spip('mailsubscribers_fonctions');
	include_spip('inc/mailsubscribers');
	include_spip('inc/config');

	if (is_null($email)) {
		$arg = mailsubscribers_verifier_args_action('subscribe');
		if ($arg){
			list($email, $identifiant) = $arg;
		}
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
		$status = (isset($infos['subscriptions'][$identifiant]['status'])?$infos['subscriptions'][$identifiant]['status']:'');
		$liste = sql_fetsel('id_mailsubscribinglist, titre_public', 'spip_mailsubscribinglists', 'identifiant=' . sql_quote($identifiant));
		if ($liste['titre_public']) {
			include_spip('inc/texte');
			$titre_liste = supprimer_numero(typo($liste['titre_public']));
		}
		else {
			$titre_liste = '#' . $liste['id_mailsubscribinglist'];
		}
	}

	if ($status == 'on') {
		$titre = _T('mailsubscriber:subscribe_deja_texte', array('email' => $email));
	}
	else {
		$subscribe = charger_fonction('subscribe','newsletter');
		$options = array();
		if (is_null($double_optin)) {
			$double_optin = lire_config('mailsubscribers/double_optin', 0);
		}

		$env = array(
			'email' => "<b>$email</b>",
			'titre_liste' => $titre_liste,
			'nom_site_spip' => $GLOBALS['meta']['nom_site'],
			'url_site_spip' => $GLOBALS['meta']['adresse_site']
		);
		if ($double_optin) {
			if ($titre_liste) {
				$titre = _T('mailsubscriber:confirmsubscribe_texte_email_liste_1', $env);
			} else {
				$titre = _T('mailsubscriber:confirmsubscribe_texte_email_1', $env);
			}
			$titre .= "<br /><br />" . _T('mailsubscriber:confirmsubscribe_texte_email_envoye');
		}
		else {
			$options['force'] = true;
			if ($titre_liste) {
				$titre = _T('mailsubscriber:subscribe_texte_email_liste_1', $env);
			} else {
				$titre = _T('mailsubscriber:subscribe_texte_email_1', $env);
			}
		}

		if ($identifiant){
			$options['listes'] = array($identifiant);
		}
		$subscribe($email, $options);
	}

	// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	include_spip('inc/minipres');
	echo minipres($titre, "<style>h1{font-weight: normal}</style>", "", true);

}
