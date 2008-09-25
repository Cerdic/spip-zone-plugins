<?php
/**************************
 * auth_openid: un plugin d'authentification OpenID pour Spip
 * (c) 2007 Edouard Lafargue
 * License: GNU/GPL
 *
 * Date: 24.03.2007
 *************************/

/*****
 * Utilisation: ce plugin active la reconnaissance des OpenID dans le
 * formulaire de Login. Si un OpenID est identifié, il lance l'authentification
 * puis, en cas de succès, recherche si l'OpenID en question correspond à un utilisateur
 * existant sur le site. Pour cela, il recherche un utilisateur sont le champ 'url_site'
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

function action_controler_openid() {

	// La cible de notre operation de connexion
	$url = _request('url');
	$redirect = isset($url) ? $url : _DIR_RESTREINT_ABS;
	
	// Verifier l'openid revenant
	include_spip('inc/openid');
	terminer_authentification_openid($redirect);

}

?>
