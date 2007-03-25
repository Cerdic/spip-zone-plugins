<?php

// Balise LOGIN_PUBLIC qui supporte l'OpenID

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
spip_connect();

function balise_LOGIN_PUBLIC ($p, $nom='LOGIN_PUBLIC') {
	return calculer_balise_dynamique($p, $nom, array('url'));
}

# retourner:
# 1. l'url collectee ci-dessus (args0) ou donnee en filtre (filtre0)
# 2. l'eventuel parametre de la balise (args1) fournie par
#    calculer_balise_dynamique, en l'occurence le #LOGIN courant si l'on
#    programme une <boucle(AUTEURS)>[(#LOGIN_PUBLIC{#LOGIN})]

function balise_LOGIN_PUBLIC_stat ($args, $filtres) {
	return array($filtres[0] ? $filtres[0] : $args[0], $args[1], $args[2]);
}

function balise_LOGIN_PUBLIC_dyn($url, $login) {

	if (!$url 		# pas d'url passee en filtre ou dans le contexte
	AND !$url = _request('url') # ni d'url passee par l'utilisateur
	)
		$url = str_replace('&amp;', '&', self());
	return login_explicite($login, $url);
}

function login_explicite($login, $cible) {
	global $auteur_session;

	$action = str_replace('&amp;', '&', self());
	if ($cible) {
		$cible = parametre_url($cible, 'var_erreur', '', '&');
		$cible = parametre_url($cible, 'var_login', '', '&');
	} else {
		if (ereg("[?&]url=([^&]*)", $action, $m))
			$cible = rawurldecode($m[1]);
		else
			$cible = _DIR_RESTREINT ;
	}

	verifier_visiteur();

	// Si on est connecte, envoyer vers la destination
	// sauf si on y est deja
	if ($auteur_session AND
	($auteur_session['statut']=='0minirezo'
	OR $auteur_session['statut']=='1comite')) {
		if ($cible != $action) {
			if (!headers_sent() AND !$_GET['var_mode'])
				redirige_par_entete($cible);
			else {
				include_spip('inc/minipres');
				return http_href($cible, _T('login_par_ici'));
			}
		} else
			return ''; # on est arrive on bon endroit, et logue'...
	}
	return login_pour_tous($login ? $login : _request('var_login'), $cible, $action);
}

function login_pour_tous($login, $cible, $action) {
	global $ignore_auth_http, $_SERVER, $_COOKIE;
	global $consumer;

	// en cas d'echec de cookie, inc_auth a renvoye vers le script de
	// pose de cookie ; s'il n'est pas la, c'est echec cookie
	// s'il est la, c'est probablement un bookmark sur bonjour=oui,
	// et pas un echec cookie.
	if (_request('var_echec_cookie'))
		$echec_cookie = ($_COOKIE['spip_session'] != 'test_echec_cookie');
	else $echec_cookie = '';

	$pose_cookie = generer_url_public('spip_cookie');
	$auth_http = '';	
	if ($echec_cookie AND !$ignore_auth_http) {
		include_spip('inc/headers');
		if (php_module()) $auth_http = $pose_cookie;
	}
	// Attention dans le cas 'intranet' la proposition de se loger
	// par auth_http peut conduire a l'echec.
	if (isset($_SERVER['PHP_AUTH_USER']) AND isset($_SERVER['PHP_AUTH_PW']))
		$auth_http = '';

	// Le login est memorise dans le cookie d'admin eventuel
	if (!$login) {
		if (ereg("^@(.*)$", $_COOKIE['spip_admin'], $regs))
			$login = $regs[1];
	} else if ($login == '-1')
		$login = '';

	$erreur = '';
	if ($login) {
	// Detection si il s'agit d'un URL à traiter comme un openID
	// RFC3986 Regular expression for matching URIs
	preg_match('_^(?:([^:/?#]+):)?(?://([^/?#]*))?'.
                   '([^?#]*)(?:\?([^#]*))?(?:#(.*))?$_',
                   $login, $uri_parts);
	if ($uri_parts[1] == "http" OR $uri_parts[1] == "https") {

 		spip_log("Traitement login OpenID");
		session_start();

		$scheme = 'http';
		if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
		    $scheme .= 's';
		}

		$openid = $login;
		$process_url = sprintf("$scheme://%s:%s%sspip.php?action=cookie_openid",
	                       $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'],
	                       dirname($_SERVER['PHP_SELF']));

		$trust_root = sprintf("$scheme://%s:%s%s",
	                      $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'],
	                      dirname($_SERVER['PHP_SELF']));

		// Begin the OpenID authentication process.
		$auth_request = $consumer->begin($login);

		// Handle failure status return values.
		if (!$auth_request) {
// TODO: Translation
		    $erreur = "Erreur d'authentification OpenID: avez-vous bien entr&eacute; un OpenID valide?";
		  $row = array();
		  $login = '';
                  include_spip('inc/cookie');
                  spip_setcookie("spip_admin", "", time() - 3600);
                } else {
		// Redirect the user to the OpenID server for authentication.  Store
		// the token for this authentication so we can verify the response.

		$redirect_url = $auth_request->redirectURL($trust_root,
                                           $process_url);

		header("Location: ".$redirect_url);
//		return Array();

		} 
		} else {

		$row =  spip_abstract_fetsel('*', 'spip_auteurs', "login=" . spip_abstract_quote($login));
		// Retrouver ceux qui signent de leur nom ou email
		if (!$row AND !$GLOBALS['ldap_present']) {
			$row = spip_abstract_fetsel('*', 'spip_auteurs', "(nom = " . spip_abstract_quote($login) . " OR email = " . spip_abstract_quote($login) . ") AND login<>'' AND statut<>'5poubelle'");
			if ($row) {
				$login_alt = $login; # afficher ce qu'on a tape
				$login = $row['login'];
			}
		}

		if ((!$row AND !$GLOBALS['ldap_present']) OR
			($row['statut'] == '5poubelle') OR 
			(($row['source'] == 'spip') AND $row['pass'] == '')) {
			$erreur =  _T('login_identifiant_inconnu',
				array('login' => htmlspecialchars($login)));
			$row = array();
			$login = '';
			include_spip('inc/cookie');
			spip_setcookie("spip_admin", "", time() - 3600);
		} else {
			// on laisse le menu decider de la langue
			unset($row['lang']);
		}
	   }
	}
	if (!$row)
		$row = array();
	// afficher "erreur de mot de passe" si &var_erreur=pass
	if (_request('var_erreur') == 'pass')
		$erreur = _T('login_erreur_pass');

	// afficher le code de retour d'erreur OpenID si var_erreur=openid
	if (_request('var_erreur') == 'openid')
		$erreur = "Erreur OpenID: " . _request('openid_error');

	// le formulaire utilise le filtre |chercher_logo si un id_auteur est la...
	include_spip('inc/logos');

	// Appeler le squelette formulaire_login
	return array('formulaires/formulaire_login', $GLOBALS['delais'],
		array_merge(
				array_map('texte_script', $row),
				array(
					'action2' => ($login ? $pose_cookie: $action),
					'erreur' => $erreur,
					'action' => $action,
					'url' => $cible,
					'auth_http' => $auth_http,
					'echec_cookie' => ($echec_cookie ? ' ' : ''),
					'login' => $login,
					'login_alt' => (isset($login_alt) ? $login_alt : $login),
					'self' => str_replace('&amp;', '&', self())
					)
				)
			);

}

// Bouton duree de connexion

function filtre_rester_connecte($prefs) {
	$prefs = unserialize(stripslashes($prefs));
	return $prefs['cnx'] == 'perma' ? ' ' : '';
}

?>
