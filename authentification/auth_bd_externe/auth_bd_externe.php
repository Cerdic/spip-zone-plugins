<?php
$GLOBALS['bd_externe_present'] = TRUE;
$GLOBALS['ldap_present'] = TRUE; // Hack pour ne pas avoir à modifier /formulaires/inc-login_public.php

// Lecture du paramétrage BD externe
require_once(dirname(__FILE__).'/inc/meta_auth_bd_externe.php');
$bd_externe=lire_parametrage_auth_bd_externe ();


// Inclusion fonctions diverses
require_once(dirname(__FILE__).'/inc/utils.php');

function action_cookie() {
	include_spip('inc/session');
	include_spip('inc/cookie');
	action_spip_cookie();
}

function action_spip_cookie()
{
	
  global
    $auteur_session,
    $change_session,
    $cookie_admin,
    $cookie_session,
    $essai_auth_http,
    $essai_login,
    $id_auteur,
    $ignore_auth_http,
    $bd_externe_present,
    $logout,
    $logout_public,
    $next_session_password_md5,
    $retour,
    $session_login,
    $session_login_hidden,
    $session_password,
    $session_password_md5,
    $session_remember,
    $spip_admin,
    $spip_session,
    $test_echec_cookie,
    $url,
    $valeur,
    $var_lang,
    $var_lang_ecrire;

// rejoue le cookie pour renouveler spip_session
if ($change_session == 'oui') {
	if (verifier_session($spip_session)) {
		// Attention : seul celui qui a le bon IP a le droit de rejouer,
		// ainsi un eventuel voleur de cookie ne pourrait pas deconnecter
		// sa victime, mais se ferait deconnecter par elle.
		if ($auteur_session['hash_env'] == hash_env()) {
			spip_log("rejoue session");
			$auteur_session['ip_change'] = false;
			$cookie = creer_cookie_session($auteur_session);
			supprimer_session($spip_session);
			spip_setcookie('spip_session', $cookie);
		}
		else
			spip_log("session non rejouee, changement d'IP ?");
	}
	envoie_image_vide();echo "ok";
	exit;
}

// tentative de connexion en auth_http
if ($essai_auth_http AND !$ignore_auth_http) {
	auth_http(($url ? $url : _DIR_RESTREINT_ABS), $essai_auth_http);
	exit;
}

// cas particulier, logout dans l'espace public
if ($logout_public) {
	$logout = $logout_public;
	if (!$url)  $url = $GLOBALS['meta']['adresse_site'];
 }
// tentative de logout
if ($logout) {
	if ($auteur_session['login'] == $logout) {
		spip_query("UPDATE spip_auteurs SET en_ligne = DATE_SUB(NOW(),INTERVAL 6 MINUTE) WHERE id_auteur = ".$auteur_session['id_auteur']);
		if ($spip_session) {
			zap_sessions($auteur_session['id_auteur'], true);
			spip_setcookie('spip_session', $spip_session, time() - 3600 * 24);
		}
		
		if ($_SERVER['PHP_AUTH_USER']
		AND !$ignore_auth_http
		AND verifier_php_auth()) {
			auth_http(($url ? $url : _DIR_RESTREINT_ABS), 'logout');
		}
		unset ($auteur_session);
	}
	spip_log("logout: $logout");
	redirige_par_entete($url ? $url : generer_url_public('login'));
}

// en cas de login sur bonjour=oui, on tente de poser un cookie
// puis de passer au login qui diagnostiquera l'echec de cookie
// le cas echeant.
if ($test_echec_cookie == 'oui') {
	spip_setcookie('spip_session', 'test_echec_cookie');
	redirige_par_entete(generer_url_public('login'),
			    "var_echec_cookie=oui&url="
			    . ($url ? urlencode($url) : _DIR_RESTREINT_ABS), true);
}

// Tentative de login
unset ($cookie_session);
$redirect = ($url ? $url : _DIR_RESTREINT_ABS);
if ($essai_login == "oui") {
	// Recuperer le login en champ hidden
	if ($session_login_hidden AND !$session_login)
		$session_login = $session_login_hidden;

	$login = $session_login;

	// Essayer differentes methodes d'authentification
	$auths = array('spip');
	spip_connect(); // pour savoir si ldap est present 
	
	if ($bd_externe_present) $auths[] = 'bd_externe'; 
	$ok = false;
	
	include_spip('inc/auth_spip'); // Hack : on sort cela de la boucle foreach
	foreach ($auths as $nom_auth) {
			
		$classe_auth = "Auth_".$nom_auth;		
		$auth = new $classe_auth;
		
		if ($auth->init()) {
			
			// Essayer les mots de passe challenge md5					
			$ok = $auth->verifier_challenge_md5($login, $session_password_md5, $next_session_password_md5);	
				
			// Sinon essayer avec le mot de passe autre cryptage	
			if (!$ok && $session_password) $ok = $auth->verifier($login, $session_password);			
			if ($ok)  { $auth->lire(); break; }
		}
	}

	// Si la connexion a reussi
	if ($ok) {
		
		// Nouveau redacteur ou visiteur inscrit par mail :
		// 'nouveau' -> '1comite' ou  '6forum'
		// Si LDAP : importer l'utilisateur vers la base SPIP
		$auth->activer();

		if ($auth->login AND $auth->statut == '0minirezo') // force le cookie pour les admins
			$cookie_admin = "@".$auth->login;

		// On est connecte : recuperer les donnees auteurs
		// poser le cookie session, puis le cas echeant
		// verifier que le statut correspond au minimum requis,
		$query = "SELECT * FROM spip_auteurs WHERE login='".addslashes($auth->login)."'";
		$result = spip_query($query);
		if ($row_auteur = spip_fetch_array($result)) {
		
		//if ($row_auteur = $auth->auteur(addslashes($auth->login))) {
			
			$cookie_session = creer_cookie_session($row_auteur);
			
		} else
			$ok = false;

		// Si on se connecte dans l'espace prive, ajouter "bonjour" (inutilise)
		if ($ok AND ereg(_DIR_RESTREINT_ABS, $redirect)) {
			$redirect .= ((false !== strpos($redirect, "?")) ? "&" : "?")
			. 'bonjour=oui';
		}
		
	}

	if (!$ok) {
		if (ereg(_DIR_RESTREINT_ABS, $redirect))
			$redirect = generer_url_public('login',
				"var_login=$login", true);
		if ($session_password || $session_password_md5)
			$redirect .= '&var_erreur=pass';
		$redirect .= '&url=' . urlencode($url);
		spip_log("echec login: $login");
	}
	else
		spip_log("login: $login");
}

// cookie d'admin ?
if ($cookie_admin == "non") {
	if (!$retour)
		$retour = generer_url_public('login',
			'url='.urlencode($url), true);

	spip_setcookie('spip_admin', $spip_admin, time() - 3600 * 24);
	$redirect = ereg_replace("([?&])var_login=[^&]*&?", '\1', $retour);
	$redirect = ereg_replace("([?&])var_erreur=[^&]*&?", '\1', $redirect);
	$redirect .= ((false !== strpos($redirect, "?")) ? "&" : "?")
		. "var_login=-1";
}
else if ($cookie_admin AND $spip_admin != $cookie_admin) {
	spip_setcookie('spip_admin', $cookie_admin, time() + 3600 * 24 * 14);
}

// cookie de session ?

if ($cookie_session) {
	if ($session_remember == 'oui')
		spip_setcookie('spip_session', $cookie_session, time() + 3600 * 24 * 14);
	else
		spip_setcookie('spip_session', $cookie_session);

	$prefs = ($row_auteur['prefs']) ? unserialize($row_auteur['prefs']) : array();
	$prefs['cnx'] = ($session_remember == 'oui') ? 'perma' : '';
	
	spip_query ("UPDATE spip_auteurs SET prefs = '".addslashes(serialize($prefs))."' WHERE id_auteur = ".$row_auteur['id_auteur']);

}

// changement de langue espace public
if ($var_lang) {
	include_spip('inc/lang');

	if (changer_langue($var_lang)) {
		spip_setcookie('spip_lang', $var_lang, time() + 365 * 24 * 3600);
		$redirect = ereg_replace("[?&]lang=[^&]*", '', $redirect);
		$redirect .= (strpos($redirect, "?")!==false ? "&" : "?") . "lang=$var_lang";
	}
}

// changer de langue espace prive (ou login)
if ($var_lang_ecrire) {
	include_spip('inc/lang');

	if (changer_langue($var_lang_ecrire)) {
		spip_setcookie('spip_lang_ecrire', $var_lang_ecrire, time() + 365 * 24 * 3600);
		spip_setcookie('spip_lang', $var_lang_ecrire, time() + 365 * 24 * 3600);

		if (_FILE_CONNECT AND $id_auteur) {
			include_spip('inc/admin');
			if (verifier_action_auteur('var_lang_ecrire', $valeur, $id_auteur)) {
				spip_query ("UPDATE spip_auteurs SET lang = '".addslashes($var_lang_ecrire)."' WHERE id_auteur = ".$id_auteur);
				$auteur_session['lang'] = $var_lang_ecrire;
				ajouter_session($auteur_session, $spip_session);	// enregistrer dans le fichier de session
			}
		}

		$redirect = ereg_replace("[?&]lang=[^&]*", '', $redirect);
		$redirect .= (strpos($redirect, "?")!==false ? "&" : "?") . "lang=$var_lang_ecrire";
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

class Auth_bd_externe {
	var $nom, $bio, $email, $nom_site, $url_site, $login, $pass, $statut, $pgp;

	function init() {		
		global $bd_externe;
		
		spip_log("entree dans auth_bd_externe");
		
		// Verifier la presence de bd_externe
		if (!$GLOBALS['bd_externe_present']) return false;
		
		// Inclusion des fonctions d'accès à la BD basées : mysql ou Pear DB
		if ($bd_externe['serveur']=="mysql") require_once(dirname(__FILE__).'/inc/mysql_query.php');
		else require_once(dirname(__FILE__).'/inc/pear_query.php');

		return (bd_externe_connect());
	}

	function verifier_challenge_md5($login, $mdpass_actuel, $mdpass_futur) {
		return false;
	}

	function verifier($login, $pass) {
		global $bd_externe;
		global $bd_externe_link;

		
		$cond_supp = '';
		
		// Securite...
		if (!$login || !$pass) return false;

		switch($bd_externe['type_passwd']) {
			case 'clear_text' : 
				$local_pass = $pass;
				break;
			case 'md5' : 
				$local_pass = md5($pass);
				break;
			case 'challenge_md5' : 			
				$cond_supp = ', '.$bd_externe['champ_alea'].' AS alea'; //Select des alea.
				break;
			case 'crypt' : 
				$local_pass = crypt($pass);
				break;
			case 'unix' :
				// On ne fait rien pour le moment car on a besoin de récupérer le salt
				break;			
		}
			
		$query = "SELECT ".$bd_externe['champ_login_ext']." AS login_ext, ".$bd_externe['champ_passwd']." AS pass".$cond_supp." FROM ".$bd_externe['table'];
		if ($bd_externe['table_jointure']!="") {
			$query.=", ".$bd_externe['table_jointure'];
			$query=AjouteClause($query,$bd_externe['table'].".".$bd_externe['champ_cle']."=".$bd_externe['table_jointure'].".".$bd_externe['champ_cle']);
		}
	
		$query=AjouteClause($query,$bd_externe['champ_login_ext']."='".addslashes($login)."'");
		
		if ($bd_externe['champ_statut']!="") {			
			
			// Clauses supplémentaires pour détecter le cas échéants auteurs et administrateurs
			$cond_supp2="";
			if ($bd_externe['val_redacteur']!="") {
				$val_redacteur=explode(";",$bd_externe['val_redacteur']);			
				foreach($val_redacteur as $cle => $val) $cond_supp2=AjouteClauseCond($cond_supp2,$bd_externe['champ_statut']."='$val'","OR");
			}
			if ($bd_externe['val_administrateur']!="") {	
				$val_administrateur=explode(";",$bd_externe['val_administrateur']);			
				foreach($val_administrateur as $cle => $val) $cond_supp2=AjouteClauseCond($cond_supp2,$bd_externe['champ_statut']."='$val'","OR");			
			}
			$cond_supp2=AjouteClauseCond($cond_supp2,"");
			$query.=$cond_supp2;
		}

		$result = bd_externe_query($query);
		
		if ($row = bd_externe_fetch($result)) {
			if ($bd_externe['type_passwd'] == 'challenge_md5') $local_pass = md5($row['alea'].$pass);			
			if ($bd_externe['type_passwd'] == 'unix') $local_pass=crypt($pass,'$1$'.substr($row['pass'],3,8)); // Modifier le substr pour tenir compte du nombre de caractères utilisés dans le salt
			if (addslashes($login) == $row['login_ext'] AND $local_pass == $row['pass']) {				
				$this->login = $login;
				
				return true;
			}
		}
		return false;
	}

	function lire() {
		
		global $bd_externe;
		global $bd_externe_link;
		
		$this->nom = $this->email = $this->bio = $this->nom_site = $this->url_site = $this->pgp = $this->pass = $this->statut = '';		

		if (!$this->login) return false;

		// Si l'auteur existe dans la base, y recuperer les infos
		$query = "SELECT * FROM spip_auteurs WHERE login='".addslashes($this->login)."' AND source='bd_externe'";
		$result = spip_query($query);

		if ($row = spip_fetch_array($result)) {
			$this->nom = $row['nom'];
			$this->bio = $row['bio'];
			$this->email = $row['email'];
			$this->nom_site = $row['nom_site'];
			$this->url_site = $row['url_site'];
			$this->statut = $row['statut'];
			$this->pgp = $row['pgp'];
			return true;
		}

		
		// Lire les infos sur l'auteur depuis la base externe
		$query = "SELECT * FROM ".$bd_externe['table'];
		if ($bd_externe['table_jointure']!="") {
			$query.=", ".$bd_externe['table_jointure'];
			$query=AjouteClause($query,$bd_externe['table'].".".$bd_externe['champ_cle']."=".$bd_externe['table_jointure'].".".$bd_externe['champ_cle']);
		}
		$query=AjouteClause($query,$bd_externe['champ_login_ext']."='".addslashes($this->login)."'");


		$result = bd_externe_query($query);

		if ($rows = bd_externe_fetch($result) ) {
			$this->nom="";
			if ($bd_externe['champ_prenom']!="") $this->nom= ucwords(strtolower($rows[$bd_externe['champ_prenom']]))." ";
			$this->nom .= strtoupper($rows[$bd_externe['champ_nom']]);
			if (!$this->bio) $this->bio = $rows[$bd_externe['champ_bio']];
			if (!$this->email) $this->email = $rows[$bd_externe['champ_email']];
			if (!$this->nom_site) $this->nom_site = $rows[$bd_externe['champ_nom_site']];
			if (!$this->url_site) $this->url_site = $rows[$bd_externe['champ_url_site']];
			if (!$this->pgp) $this->pgp = $rows[$bd_externe['champ_pgp']];
			
			if ($bd_externe['champ_statut']!="") {
				$this->statut="";
				$val_redacteur=explode(";",$bd_externe['val_redacteur']);					
				$val_administrateur=explode(";",$bd_externe['val_administrateur']);				
				foreach($val_redacteur as $cle => $val) if ($rows[$bd_externe['champ_statut']]==$val) $this->statut="1comite";
				foreach($val_administrateur as $cle => $val) if ($rows[$bd_externe['champ_statut']]==$val) $this->statut="0minirezo";
			}
			else $this->statut="1comite";
			return true;
		}
		return false;
	}

	function activer() {

		$nom = addslashes($this->nom);
		$bio = addslashes($this->bio);
		$login = addslashes($this->login);
		$email = addslashes($this->email);
		$nom_site = addslashes($this->nom_site);
		$url_site = addslashes($this->url_site);		
		$pgp = addslashes($this->pgp);
		$statut=$this->statut;

		if ($statut=="") exit;

		// Si l'auteur n'existe pas, l'inserer avec le statut par defaut (defini a l'install)
		$query = "SELECT id_auteur FROM spip_auteurs WHERE login='$login'";
		$result = spip_query($query);
		if (spip_num_rows($result)) return false;

		$query = "INSERT IGNORE INTO spip_auteurs (source, nom, login, email, bio, nom_site, url_site, pgp, statut, pass) ".
			"VALUES ('bd_externe', '$nom', '$login', '$email', '$bio', '$nom_site', '$url_site', '$pgp', '$statut', '')";

		return spip_query($query);
	}
	
	function auteur($login) {
		global $bd_externe;
		global $bd_externe_link;
		
		$query = "SELECT * FROM vhffs_users, vhffs_user_info WHERE (vhffs_users.uid=vhffs_user_info.uid) AND (username='$login' )";	
		$result = bd_externe_query($query);
		if ($row = bd_externe_fetch($result)) {
			$row['id_auteur']=$row['uid'];
			$row['statut']="0minirezo";
			return($row);
		}
	}
}
?>