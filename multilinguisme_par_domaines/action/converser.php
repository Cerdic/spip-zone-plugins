<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2016                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion de l'action converser qui permet changer de langue
 *
 * @package SPIP\Core\Langue
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/cookie');

/**
 * Point d'entrée pour changer de langue
 *
 * Pas de secu si espace public ou login ou installation
 * mais alors on n'accède pas à la base, on pose seulement le cookie.
 *
 * @return void
 */
function action_converser() {
	include_spip('action/converser');
	$update_session = false;
	if (_request('arg') and spip_connect()) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$securiser_action();
		$update_session = true;
	}

	$lang = action_converser_changer_langue($update_session);
	$redirect = rawurldecode(_request('redirect'));

	include_spip('multilinguisme_par_domaines_fonctions');
	if (_request('var_lang')) {
		$domaine_redirect = multilinguisme_par_domaines_trouver_url_lang(_request('var_lang'));
		if ($redirect) {
			$hote_en_cours = parse_url( $redirect, PHP_URL_HOST );
			$hote_redirection = parse_url( $domaine_redirect, PHP_URL_HOST );
			$redirect = str_replace($hote_en_cours, $hote_redirection, $redirect);
			$redirect = parametre_url($redirect, 'lang', '');
		}
	}

	if (!$redirect) {
		$redirect = _DIR_RESTREINT_ABS;
	}
	redirige_par_entete($redirect, true);
}

/**
 * Cette fonction prépare le travail de changement de langue
 * en récupérant la bonne variable de langue
 *
 * @global array $GLOBALS ['visiteur_session']
 * @param bool $update_session
 * @return string
 */
function action_converser_changer_langue($update_session) {
	if ($lang = _request('var_lang')) {
		action_converser_post($lang);
	} elseif ($lang = _request('var_lang_ecrire')) {
		if ($update_session) {
			sql_updateq("spip_auteurs", array("lang" => $lang), "id_auteur = " . $GLOBALS['visiteur_session']['id_auteur']);
			$GLOBALS['visiteur_session']['lang'] = $lang;
			$session = charger_fonction('session', 'inc');
			if ($spip_session = $session($GLOBALS['visiteur_session'])) {
				spip_setcookie(
					'spip_session',
					$spip_session,
					time() + 3600 * 24 * 14
				);
			}
		}
		action_converser_post($lang, 'spip_lang_ecrire');
	}

	return $lang;
}

/**
 * Cette fonction effectue le travail de changement de langue
 *
 * @param string $lang
 * @param bool $ecrire
 * @return void
 */
function action_converser_post($lang, $ecrire = false) {
	if ($lang) {
		include_spip('inc/lang');
		if (changer_langue($lang)) {
			spip_setcookie('spip_lang', $_COOKIE['spip_lang'] = $lang, time() + 365 * 24 * 3600);
			if ($ecrire) {
				spip_setcookie('spip_lang_ecrire', $_COOKIE['spip_lang_ecrire'] = $lang, time() + 365 * 24 * 3600);
			}
		}
	}
}
