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
 * Pour l'instant, ce plugin de désactive pas le login/mot de passe qui reste compatible avec
 * le système actuel, au cas où l'IDP OpenID n'est pas dispo (ce qui peut arriver).
 *
 * Commentaires sur l'utilisation du champ "url_site": c'est un champ qui se prête bien à
 * être un OpenID, puisqu'en général, les blogs qui sont des IDP donnent comme OpenID l'adresse
 * du blog des utilisateurs, et que grâce au modèle de délégation OpenID, tout utilisateur peut
 * effectivement utiliser l'adresse de son site perso comme OpenID.
 ****/

include_spip('inc/actions');
include_spip('inc/cookie');


// Cette fonction est appelée lors du retour de l'authentification OpenID
// Elle doit vérifier si l'authent est OK, puis chercher l'utilisateur
// associé dans spip (champ URL dans la base), et finalement l'authentifier
// en créant le bon cookie.
//
// Idéalement, il suffirait de coder une méthode d'authent supplémentaire
// dans le noyeau de Spip...
//
// Malheureusement, les poitns d'entrée Spip manquent donc on est obilgé
// de recopier pas mal du code de action_cookie_dist pour faire marcher le truc

function action_cookie_openid() {
        global
	    $auteur_session,
	    $change_session,
	    $cookie_admin,
	    $cookie_session,
 	    $essai_auth_http,
	    $essai_login,
	    $ignore_auth_http,
	    $retour,
	    $session_login,
	    $session_login_hidden,
	    $session_password,
	    $session_password_md5,
	    $session_remember,
	    $spip_admin,
	    $test_echec_cookie,
	    $url,
	    $hash,
	    $var_lang,
	    $var_lang_ecrire;

	// Recupération session (à comprendre ??)
	session_start();
	$redirect = ($url ? $url : _DIR_RESTREINT_ABS);
	spip_log("[auth_openid] Got url: " . $url . " and redirect:" . $redirect);
	// Complete the authentication process using the server's response.
	include_spip('inc/openid');
	$consumer = init_auth_openid();
	$response = $consumer->complete($_GET);

	if ($response->status == Auth_OpenID_CANCEL) {
	    // This means the authentication was cancelled.
	    spip_log('[auth_openid] Verification cancelled.');
	    $redirect = generer_url_public("login","var_erreur=openid&openid_error=" . _T('authopenid:verif_refusee'),'&');
	} else if ($response->status == Auth_OpenID_FAILURE) {
	    spip_log( "[auth_openid] OpenID authentication failed: " . $response->message);
	    $redirect = generer_url_public("login","var_erreur=openid&openid_error=" . rawurlencode($response->message), '&');
	} else if ($response->status == Auth_OpenID_SUCCESS) {
	    // This means the authentication succeeded.
	    $openid = $response->identity_url;
	    $esc_identity = htmlspecialchars($openid, ENT_QUOTES);
	    spip_log("[auth_openid] Successful OpenID auth of $openid");

	    // Maintenant, il faut récupérer le username correspondant à cet OpenID
            $result = spip_query("SELECT login FROM spip_auteurs WHERE url_site=" . spip_abstract_quote($esc_identity) . " AND statut<>'5poubelle'"); 
            $row = spip_fetch_array($result);
	    if (!$row) {
		spip_log("[auth_openid] No user here has this OpenID");
		$redirect = generer_url_public("login","var_erreur=openid&openid_error=" . _T('authopenid:utilisateur_inconnu'),'&');
	    } else {
		// On récupère les données de l'utilisateur dans $row_auteur:
         	$result = spip_query("SELECT * FROM spip_auteurs WHERE login=" . spip_abstract_quote($row['login']) . " AND statut<>'5poubelle'"); 
        	$row_auteur = spip_fetch_array($result);

		// Je ne sais pas à quoi cela sert, mais c'est dans action_cookie_dist:
	        if ($row_auteur['statut'] == 'nouveau') {
       	         include_spip('inc/auth');
       	         $row_auteur['statut'] = acces_statut($row_auteur['id_auteur'], $row_auteur['statut'], $row_auteur['bio']);
       		 }

                spip_log("[auth_openid] OpenID login de " . $row_auteur['login'] . " vers $redirect");

                if ($row_auteur['statut'] == '0minirezo')
                        $cookie_admin = "@".$session_login;

                $var_f = charger_fonction('session', 'inc');
                $cookie_session = $var_f($row_auteur);

                if ($session_remember == 'oui')
                        spip_setcookie('spip_session', $cookie_session, time() + 3600 * 24 * 14);
                else
                        spip_setcookie('spip_session', $cookie_session);

                $prefs = ($row_auteur['prefs']) ? unserialize($row_auteur['prefs']) : array();
                $prefs['cnx'] = ($session_remember == 'oui') ? 'perma' : '';

     	spip_query("UPDATE spip_auteurs SET prefs = " . spip_abstract_quote(serialize($prefs)) . " WHERE id_auteur = " . $row_auteur['id_auteur']);

		// cookie d'admin ?
		if ($cookie_admin == "non") {
 		       if (!$retour)
		                $retour = generer_url_public('login',
        	        	        'url='.rawurlencode($url), true);
	
	        spip_setcookie('spip_admin', $spip_admin, time() - 3600 * 24);
	        $redirect = parametre_url($retour,'var_login','','&');
	        $redirect = parametre_url($redirect,'var_erreur','','&');
	        $redirect .= ((false !== strpos($redirect, "?")) ? "&" : "?")
		                . "var_login=-1";
		}
		else if ($cookie_admin AND $spip_admin != $cookie_admin) {
		        spip_setcookie('spip_admin', $cookie_admin, time() + 3600 * 24 * 14);
		 }
	}
	}
	// Redirection
	// Sous Apache, les cookies avec une redirection fonctionnent
	// Sinon, on fait un refresh HTTP
	if (ereg("^Apache", $GLOBALS['SERVER_SOFTWARE'])) {
	        redirige_par_entete($redirect);
	}
	else {
        include_spip('inc/headers');
	        spip_header("Refresh: 0; url=" . $redirect);
	        echo "<html><head>";
	        echo "<meta http-equiv='Refresh' content='0; url=".$redirect."'>";
	        echo "</head>\n";
	        echo "<body><a href='".$redirect."'>"._T('navigateur_pas_redirige')."</a></body></html>";
		}
}

?>
