<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/*****
 * Utilisation: ce plugin active la reconnaissance des OpenID dans le
 * formulaire de Login. Si un OpenID est identifié, il lance l'authentification
 * puis, en cas de succès, recherche si l'OpenID en question correspond à un utilisateur
 * existant sur le site. Pour cela, il recherche un utilisateur dont le champ 'url_site'
 * est égal à cet OpenID. Si aucun n'existe, erreur. Sinon il écrit le cookie de session
 * et redirige vers la première page
 *
 * Sécurité: a priori, pas de dangers majeurs: l'utilisateur décide lui-même d'indiquer son
 * OpenID, si il met un OpenID non fiable ou appartenant à qqun d'autre, il donne effectivement
 * accès à son compte par cette autre personne, mais c'est équivalent à donner son mot de passe
 * à une tierce personn.
 *
 * Pour l'instant, ce plugin ne désactive pas le login/mot de passe qui reste compatible avec
 * le système actuel, au cas où l'IDP OpenID n'est pas dispo (ce qui peut arriver).
 *
 ****/

include_spip('inc/actions');
include_spip('inc/cookie');


// Cette fonction est appelee lors du retour de l'authentification OpenID
// Elle doit verifier si l'authent est OK, puis chercher l'utilisateur
// associé dans spip (champ openid dans la base), et finalement l'authentifier
// en creant le bon cookie.

function action_controler_openid_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!$login = $arg) {
		spip_log("action_controler_openid_dist $arg pas compris");
	}
	else {
		include_spip('auth/openid');
		$res = auth_openid_terminer_identifier_login($login);

		if (is_string($res)){ // Erreur
			$redirect = _request('redirect');
			$redirect = parametre_url($redirect,'var_erreur',$res);
			include_spip('inc/headers');
			redirige_par_entete($redirect);
		}

		// sinon on loge l'auteur identifie, et on finit (redirection automatique)
		auth_loger($res);
	}

}

?>
