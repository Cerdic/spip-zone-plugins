<?php
// Override tiré de SPIP 2.08 pour pouvoir poser le cookie de langue par converser() même en SPIP 1.9
// Rien à faire pour SPIP 2
if (version_compare($spip_version, '2.0.0', '>=')) return; 

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cookie');

// changer de langue: pas de secu si espace public ou login ou installation
// mais alors on n'accede pas a la base, on pose seulement le cookie.

// http://doc.spip.org/@action_converser_dist
function action_converser()
{
	if ($lang = _request('var_lang'))
		action_converser_post_208($lang);
	elseif ($lang = _request('var_lang_ecrire')) {
		if ( _request('arg') AND spip_connect()) {
			$securiser_action = charger_fonction('securiser_action', 'inc');
			$securiser_action();

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
		action_converser_post_208($lang, 'spip_lang_ecrire');
	}

	$redirect = rawurldecode(_request('redirect'));

	if (!$redirect) $redirect = _DIR_RESTREINT_ABS;
	$redirect = parametre_url($redirect,'lang',$lang,'&');
	redirige_par_entete($redirect, true);
}

// http://doc.spip.org/@action_converser_post
function action_converser_post_208($lang, $ecrire=false)
{
	if ($lang) {
		include_spip('inc/lang');
		if (changer_langue($lang)) {
			spip_setcookie('spip_lang', $_COOKIE['spip_lang'] = $lang, time() + 365 * 24 * 3600);
			if ($ecrire)
				spip_setcookie('spip_lang_ecrire', $_COOKIE['spip_lang_ecrire'] = $lang, time() + 365 * 24 * 3600);
		}
	}
}
?>