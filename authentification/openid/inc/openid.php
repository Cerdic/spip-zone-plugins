<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

@define('_OPENID_LOG', true);

/**
 * Ajout au formulaire de login
 *
 * @param string $texte
 * @param array $contexte
 * @return string
 */
function openid_login_form($texte,$contexte){
	$scriptopenid = "";
	if ($login = $contexte['var_login']
	AND $openid = sql_getfetsel('openid','spip_auteurs','login='.sql_quote($login))
	) {
		$openid = preg_replace(',^http://,i','',$openid);
		$message = _T('openid:form_login_openid_ok')  // . $openid
		. "<br />[<a href=\"#\" onclick=\"jQuery('.editer_login .explication').hide();jQuery('.editer_password').show();return false;\">"._T('openid:form_login_openid_pass')."</a>]";
		$scriptopenid = "jQuery('#var_login').keyup(function(){
			if (jQuery(this).val()!='".addslashes($login)."') {
				jQuery('.editer_login .explication').hide();
				jQuery('.editer_password').show();
			} else {
				jQuery('.editer_login .explication').show();
			}
		});";
	}
	else
		$message = _T('openid:form_login_openid');

	$texte .= "<style type='text/css'>"
	."input#var_login {width:10em;background-image : url(".find_in_path('images/login_auth_openid.gif').");background-repeat:no-repeat;background-position:center left;padding-left:18px;}\n"
	."input#password {width:10em;padding-right:18px;}\n"
	.".explication {margin:5px 0;}"
	."</style>"
	."<script type='text/javascript'>"
	."jQuery(document).ready(function(){jQuery('input#var_login').after('<div class=\'explication\'>".addslashes($message)."</div>');"
	.($scriptopenid?"if (!jQuery('.editer_password').is('.erreur')) jQuery('.editer_password').hide();":"")
	."$scriptopenid});"
	."</script>";
	return $texte;
}


/**
 * determine si un login est de type openid (une url avec http ou https)
 * @param <type> $login
 * @return <type>
 */
function is_openid($login){
	// Detection s'il s'agit d'un URL à traiter comme un openID
	// RFC3986 Regular expression for matching URIs
	#if (preg_match('_^(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(?:\?([^#]*))?(?:#(.*))?$_', $login, $uri_parts)
	#	AND ($uri_parts[1] == "http" OR $uri_parts[1] == "https")) {

	// s'il y a un point, c'est potentiellement un login openid
	// ca permet d'eliminer un bon nombre de pseudos tout en
	// autorisant les connexions openid sans avoir besoin de renseigner le http://
	if (strpos($login, '.')!==false) {
		return true;
	} else {
		return false;
	}
}

/**
 * Nettoyer et mettre en forme une url OpenID
 *
 * @param string $url_openid
 * @return string
 */
function nettoyer_openid($url_openid){
	include_spip('inc/filtres');
	$url_openid = vider_url($url_openid, false);
	$url_openid = rtrim($url_openid,'/');
	// si pas de protocole et que ca ne semble pas un email style gmail,
	// mettre http://
	if ($url_openid  AND !preg_match(';^[a-z]{3,6}://;i',$url_openid ) AND strpos($url_openid,'@')===FALSE)
		$url_openid = "http://".$url_openid;

	// pas d'ancre dans une url openid !
	// (Yahoo ajoute une ancre a l'url a son retour)
	$url_openid = preg_replace(',#[^#]*$,','',$url_openid);

	return $url_openid;
}

/**
 * Verifier qu'une url OpenID est valide
 *
 * @param string $url_openid
 */
function verifier_openid($url_openid){
	// Begin the OpenID authentication process.
	$consumer = init_auth_openid();
	openid_log("Initialisation faite", 3);
	if ($auth_request = $consumer->begin($url_openid))
		return true;
	return false;
}


/**
 * Logs pour openID, avec plusieurs niveaux pour le debug (1 a 3)
 *
 * @param mixed $data : contenu du log
 * @param int(1) $niveau : niveau de complexite du log
 * @return null
**/
function openid_log($data, $niveau=1){
	if (!defined('_OPENID_LOG') OR _OPENID_LOG < $niveau) return;
	spip_log('OpenID: '.$data, 'openid');
}



/**
 * Initialisation de l'authent OpenID
 *
 * @return Auth_OpenID_Consumer
 */
function init_auth_openid() {
	session_start();
	
	$cwd = getcwd();
	//chdir(dirname(dirname(__FILE__)));
	chdir(realpath(_DIR_OPENID_LIB));
	require_once "Auth/OpenID/Consumer.php";
	require_once "Auth/OpenID/FileStore.php";
	require_once "Auth/OpenID/SReg.php"; // Require the Simple Registration extension API.
	chdir($cwd);

	/****
	 * Répertoire temporaire où auth_openid stocke ses données
	 * afin de suivre les sessions.
	 ****/

	$store = new Auth_OpenID_FileStore(sous_repertoire(_DIR_TMP, 'auth_openid'));

	/**
	 * Create a consumer object using the store object created earlier.
	 */
	return new Auth_OpenID_Consumer($store);
}


/**
 * Lancer une demande d'auth par OpenID
 * consiste a verifier que l'url est legitime,
 * et a rediriger vers le serveur OpenID,
 * qui renverra sur l'url $retour apres identification
 *
 * Si tout se passe bien, la fonction quitte par une redirection+exit
 * En cas d'echec, la fonction renvoie une erreur
 *
 * @param string $url_openid
 * @param string $retour
 * @return string
 */
function demander_authentification_openid($url_openid, $retour){
	openid_log("Traitement login OpenID pour $url_openid",2);

	// Begin the OpenID authentication process.
	$consumer = init_auth_openid();
	openid_log("Initialisation faite", 3);
	$auth_request = $consumer->begin($url_openid);

	// Handle failure status return values.
	if (!$auth_request) {
		// ici, on peut rentrer dire que l'openid n'est pas connu...
		// plutot que de rediriger et passer la main a d'autres methodes d'auth
		openid_log("Ce login ($url_openid) n'est pas connu", 2);
		return _T('openid:erreur_openid');
	} 
	
	// l'openid donne est connu. On va donc envoyer une redirection
	// mais celle ci est differente selon la version de openid
	//
	// Dans les 2 cas, deux parametres doivent etre donnes
	// - une url de confiance, ici l'adresse du site : url_de_base()
	// - une url de redirection, sur laquelle OPENID reviendra une fois l'authentification faite (réussie ou non)
	else {
		openid_log("Le login $url_openid existe", 2);
		// argument de redirection : cette url doit etre identique
		// ici et au retour (au moins le premier parametre de l'url)
		// sinon le script openid n'est pas content
		// On peut neanmoins passer des informations supplementaires
		// nous indiquons ici une autre redirection encore, celle de l'url
		// vers laquelle le bonhomme souhaite aller (url=$cible)
		
		openid_log("Adresse de retour : $retour", 2);
		// on demande quelques informations, dont le login obligatoire
		if ($sreg_request = Auth_OpenID_SRegRequest::build(
				array('nickname'), // Required
				array('fullname', 'email')) // Optional
  		) {
			openid_log("Ajout des extensions demandees", 3);
        	$auth_request->addExtension($sreg_request);
		}

		$erreur = "";
		
		// OPENID 1
		if ($auth_request->shouldSendRedirect()) {
			openid_log("Redirection pour version 1 d'OpenID", 3);
			// Redirect the user to the OpenID server for authentication.  Store
			// the token for this authentication so we can verify the response.
			$redirect = $auth_request->redirectURL(url_de_base(), $retour);		
			openid_log("Redirection vers : $redirect", 3);
			
			// If the redirect URL can't be built, display an error message.
			if (Auth_OpenID::isFailure($redirect)) {
				openid_log("Erreur sur l'adresse de redirection : $redirect", 2);
				$erreur = openid_url_erreur(_L("Could not redirect to server: " . $redirect->message), $cible);
			}
			// pas d'erreur : redirection par entete
			else {
				openid_log("Redirection par entete", 3);
				include_spip('inc/headers');
				#redirige_par_entete($redirect);
				echo redirige_formulaire($redirect);
				exit;
			}
		}
		
		// OPENID 2
		// use a Javascript form to send a POST request to the server.
		else {
			openid_log("Redirection pour version 2 d'OpenID", 3);
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $auth_request->formMarkup(url_de_base(), $retour, false, array('id' => $form_id));
			openid_log("Redirection par formulaire : $form_html", 3);
			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html)) {
				openid_log("Erreur sur le formulaire de redirection : $form_html", 2);
				$erreur = openid_url_erreur(_L("Could not redirect to server: " . $form_html->message), $cible);
			} 
			
			// pas d'erreur : affichage du formulaire et arret du script
			else {
				openid_log("Affichage du formulaire de redirection", 3);
				$page_contents = array(
				   "<html><head><title>",
				   "OpenID transaction in progress",
				   "</title></head>",
				   "<body onload='document.getElementById(\"".$form_id."\").submit()'>",
				   $form_html,
				   "</body></html>");
				echo implode("\n", $page_contents);
				exit;
			}
		}

	}	
	
	if ($erreur) {
		openid_log("Rentrer avec l'erreur", 3);
		return $erreur;
	}
	
}


/**
 * Finir l'authentification apres le retour depuis le serveur openID
 * analyse le retour de la requete openID
 * utilise l'url de retour pour verifier la demande
 * renvoie une chaine d'erreur en cas d'erreur
 * un tableau decrivant l'utilisateur en cas de succes
 *
 * @param string $retour
 * @return mixed
 */
function terminer_authentification_openid($retour){
	openid_log("Retour du fournisseur OpenId", 2);
	
	// Complete the authentication process using the server's response.
	$consumer = init_auth_openid();
	openid_log("Initialisation faite. analyse de la reponse rendue", 2);
	$response = $consumer->complete($retour);

	// Authentification annulee par l'utilisateur
	if ($response->status == Auth_OpenID_CANCEL) {
		openid_log("Processus annule par l'utilisateur", 2);
		return _T('openid:verif_refusee');
	} 
	
	// Authentification echouee
	elseif ($response->status == Auth_OpenID_FAILURE) {
		openid_log("Echec de l'authentification chez le fournisseur", 2);
	  return _L("Authentication failed: " . $response->message);
	} 
	
	// Authentification reussie
	elseif ($response->status == Auth_OpenID_SUCCESS) {
		
		$openid = nettoyer_openid($response->identity_url); // pas de / final dans l'openid
		
		openid_log("Succes de l'authentification $openid chez le fournisseur d'identification", 1);
		// recuperer login, nom, email
		$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
		$sreg = $sreg_resp->contents();
		$identite = array(
			'login' => isset($sreg['nickname']) ? $sreg['nickname'] : '',
			'email' => isset($sreg['email']) ? $sreg['email'] : '',
			// login a defaut du nom, sinon c'est 'Nouvel auteur' qui est enregistre
			'nom' => isset($sreg['fullname']) ? $sreg['fullname'] : $sreg['nickname'],
			'openid' => $openid
		);
		return $identite;
	}
	return false;
}
/*
			#openid_log("sreg ".var_export($sreg_resp,true), 2);

			// on ajoute un auteur uniquement si les inscriptions sont autorisees sur le site
			if ($GLOBALS['meta']['accepter_inscriptions']=='oui') {
				
				openid_log("Tenter d'ajouter '$openid' dans SPIP");
				// verifier qu'on a les infos necessaires
				if (!$ok = ($couples['login'] AND $couples['email'])) {
					openid_log("Les informations transmises ne sont pas suffisantes : il manque le login et/ou l'email pour $openid.");
					$redirect = openid_url_erreur(_L("Inscription impossible : login ou email non renvoy&eacute;"), $cible);
				}
				// ajouter l'auteur si le login propose n'existe pas deja
				elseif (!$ok = openid_ajouter_auteur($couples)) {
					openid_log("Inscription impossible de '$openid' car un login ($couples[login]) existe deja dans SPIP");
					$redirect = openid_url_erreur(_L("Inscription impossible : un login identique existe deja"), $cible);
				} 
				// verifier que l'insertion s'est bien deroulee 
				else {
					if (($ok = $identifier_login($openid, "")) && $cible){					
						openid_log("Inscription de '$openid' dans SPIP OK", 3);
						$cible = parametre_url($cible,'message_ok',_L('openid:Vous &ecirc;tes maintenant inscrit et identifi&eacute; sur le site. Merci.'),'&');
					} else {
						openid_log("Echec de l'ajout de '$openid' dans SPIP", 3);
					}
				}
			}
			// rediriger si pas inscrit
			if (!$ok && !$redirect) {
				$redirect = openid_url_erreur(_L("Utilisateur OpenID inconnu dans le site)"), $cible);
			}
		}
		
		// sinon, c'est on est habilite ;)
		if ($ok) {
			openid_log("Utilisateur '$openid' connu dans SPIP, on l'authentifie", 3);
			
			// creer la session
				$session = charger_fonction('session', 'inc');
				$session($auteur);
				$p = ($auteur['prefs']) ? unserialize($auteur['prefs']) : array();
				$p['cnx'] = ($session_remember == 'oui') ? 'perma' : '';
				$p = array('prefs' => serialize($p));
				sql_updateq('spip_auteurs', $p, "id_auteur=" . $auteur['id_auteur']);
				//  bloquer ici le visiteur qui tente d'abuser de ses droits
				verifier_visiteur();			
			
			## Cette partie est identique
			## a formulaire_login_traiter
			#$auth = charger_fonction('auth','inc');
			#$auth();

			// Si on se connecte dans l'espace prive, 
			// ajouter "bonjour" (repere a peu pres les cookies desactives)
			if (openid_is_url_prive($cible)) {
				$cible = parametre_url($cible, 'bonjour', 'oui', '&');
			}
			if ($cible) {
				$cible = parametre_url($cible, 'var_login', '', '&');
			} 
			
			// transformer la cible absolue en cible relative
			// pour pas echouer quand la meta adresse_site est foireuse
			if (strncmp($cible,$u = url_de_base(),strlen($u))==0){
				$cible = "./".substr($cible,strlen($u));
			}
		
			// Si on est admin, poser le cookie de correspondance
			if ($GLOBALS['auteur_session']['statut'] == '0minirezo') {
				include_spip('inc/cookie');
				spip_setcookie('spip_admin', '@'.$GLOBALS['auteur_session']['login'],
				time() + 7 * 24 * 3600);
			}
			## /fin identique
		}
	}
	
	include_spip('inc/headers');
	redirige_par_entete($redirect?$redirect:$cible);	
}
*/

function openid_url_reception(){
	include_spip('inc/filtres');
	return url_absolue(generer_url_action("controler_openid"));
}

function openid_url_erreur($message, $cible=''){
	openid_log($message);
	if ($cible)
		$ret = $cible;
	else
		$ret = generer_url_public("login","url=".$redirect,'&'); // $redirect pas defini ici ..
	return parametre_url($ret, "var_erreur", urlencode($message),'&');
}

function openid_is_url_prive($cible){
	$parse = parse_url($cible);
	return strncmp(substr($parse['path'],-strlen(_DIR_RESTREINT_ABS)), _DIR_RESTREINT_ABS, strlen(_DIR_RESTREINT_ABS))==0;	
}

function openid_ajouter_auteur($couples){
	$statut = ($GLOBALS['openid_statut_nouvel_auteur'] 
			? $GLOBALS['openid_statut_nouvel_auteur'] 
			: '1comite');
			
	include_spip('base/abstract_sql');
	// si un utilisateur possede le meme login, on ne continue pas
	// sinon on risque de perdre l'integrite de la table
	// (pour le moment, on suppose dans la table spip_auteurs
	// qu'un login ou qu'un opentid est unique)
	if (sql_getfetsel('id_auteur','spip_auteurs','login='.sql_quote($couples['login']))) {
		return false;
	}
	$id_auteur = sql_insertq("spip_auteurs", array('statut' => $statut));
	openid_log("Creation de l'auteur '$id_auteur' pour $couples[login]", 3);
	include_spip('inc/modifier');
	revision_auteur($id_auteur, $couples);
	
	return true;
}

?>
