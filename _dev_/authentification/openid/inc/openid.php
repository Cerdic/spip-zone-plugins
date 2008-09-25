<?php

@define('_OPENID_LOG', true);

/*****
 * Initialisation de l'authent OpenID
 ****/

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



function demander_authentification_openid($login, $cible){
	openid_log("Traitement login OpenID pour $login",2);

	// Begin the OpenID authentication process.
	$consumer = init_auth_openid();
	openid_log("Initialisation faite", 3);
	$auth_request = $consumer->begin($login);

	// Handle failure status return values.
	if (!$auth_request) {
		// ici, on peut rentrer dire que l'openid n'est pas connu...
		// plutot que de rediriger et passer la main a d'autres methodes d'auth
		openid_log("Ce login ($login) n'est pas connu", 2);
		return _T('openid:erreur_openid');
	} 
	
	// l'openid donne est connu. On va donc envoyer une redirection
	// mais celle ci est differente selon la version de openid
	//
	// Dans les 2 cas, deux parametres doivent etre donnes
	// - une url de confiance, ici l'adresse du site : url_de_base()
	// - une url de redirection, sur laquelle OPENID reviendra une fois l'authentification faite (réussie ou non)
	else {
		openid_log("Le login $login existe", 2);
		// argument de redirection : cette url doit etre identique
		// ici et au retour (au moins le premier parametre de l'url)
		// sinon le script openid n'est pas content
		// On peut neanmoins passer des informations supplementaires
		// nous indiquons ici une autre redirection encore, celle de l'url
		// vers laquelle le bonhomme souhaite aller (url=$cible)
		
		// attention, il ne faut pas utiliser parametre_url() afin
		// d'encoderer $cible, ce qui casserait la transaction...
		$retour = parametre_url(openid_url_reception(), "url", url_absolue($cible), '&');
		openid_log("Adresse de retour : $retour", 2);
		// on demande quelques informations, dont le login obligatoire
		if ($sreg_request = Auth_OpenID_SRegRequest::build(
				array('nickname'), // Required
				array('fullname', 'email')) // Optional
  		) {
			openid_log("Ajout des extensions demandees", 3);
        	$auth_request->addExtension($sreg_request);
		}

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
				$redirect = openid_url_erreur(_L("Could not redirect to server: " . $redirect->message), $cible);
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
				$redirect = openid_url_erreur(_L("Could not redirect to server: " . $form_html->message), $cible);
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
	openid_log("Redirection par entete", 3);
	include_spip('inc/headers');
	redirige_par_entete($redirect);		
}



// analyse le retour de la requete openID
// et redirige vers une url en fonction
function terminer_authentification_openid($cible){
	$redirect=""; // redirection sur erreur
	openid_log("Retour du fournisseur OpenId avec : $cible", 2);
	
	// Complete the authentication process using the server's response.
	$consumer = init_auth_openid();
	openid_log("Initialisation faite. analyse de la reponse rendue", 2);
	$response = $consumer->complete(openid_url_reception());

	// This means the authentication was cancelled.	
	if ($response->status == Auth_OpenID_CANCEL) {
		openid_log("Processus annule par l'utilisateur", 2);
	    $redirect = openid_url_erreur(_T('openid:verif_refusee'), $cible); 
	} 
	
	// Authentification echouee
	elseif ($response->status == Auth_OpenID_FAILURE) {
		openid_log("Echec de l'authentification chez le fournisseur", 2);
	    $redirect = openid_url_erreur("Authentication failed: " . $response->message, $cible);
	} 
	
	// This means the authentication succeeded.
	elseif ($response->status == Auth_OpenID_SUCCESS) {
		
	    $openid = $response->identity_url;
	    $esc_identity = htmlspecialchars($openid, ENT_QUOTES);
	    openid_log("Succes de l'authentification $openid chez le fournisseur d'identification", 1);

		// identification dans SPIP
		// (charge inc/auth_openid)
		openid_log("Verification de l'identite '$openid' dans SPIP", 2);
		$identifier_login = charger_fonction('identifier_login','inc');
		if (!$ok = $identifier_login($openid, "")){ // pas de mot de passe
			// c'est ici que l'on peut ajouter un utilisateur inconnu dans SPIP
			// en plus, on connait (si la personne l'a autorise) son nom et email
			// en plus du login
			openid_log("Identite '$openid' inconnue SPIP", 2);
			
			// recuperer login, nom, email
			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();
			$couples = array(
				'login' => isset($sreg['nickname']) ? $sreg['nickname'] : '',
				'email' => isset($sreg['email']) ? $sreg['email'] : '', 
				// login a defaut du nom, sinon c'est 'Nouvel auteur' qui est enregistre
				'nom' => isset($sreg['fullname']) ? $sreg['fullname'] : $sreg['nickname'],
				'openid' => $openid
			);

			// on ajoute un auteur uniquement si les inscriptions sont autorisees sur le site
			if ($GLOBALS['meta']['accepter_inscriptions']=='oui') {
				// d'abord ajouter l'auteur si le login propose n'existe pas deja
				openid_log("Ajout de '$openid' dans SPIP");
				if (!$ok = openid_ajouter_auteur($couples)) {
					openid_log("Inscription impossible de '$openid' car un login ($couples[login]) existe deja dans SPIP");
					$redirect = openid_url_erreur(_L("Inscription impossible : un login identique existe deja"), $cible);
				} else {
					// verifier que l'insertion s'est bien deroulee 
					if (($ok = $identifier_login($openid, "")) && $cible){					
						openid_log("Inscription de '$openid' dans SPIP OK", 3);
						$cible = parametre_url($cible,'message_ok',_L('openid:Vous &ecirc;tes maintenant inscrit et identifi&eacute; sur le site. Merci.'),'&');
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


function openid_url_reception(){
	include_spip('inc/filtres');
	return url_absolue(generer_url_action("controler_openid"));
}

function openid_url_erreur($message, $cible=''){
	openid_log($message);
	if ($cible)
		$ret = $cible;
	else
		$ret = generer_url_public("login","var_erreur=openid&url=".$redirect,'&');
	return parametre_url($ret, "message_erreur", urlencode($message),'&');
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
	
	include_spip('action/editer_auteur');
	include_spip('inc/modifier');
	instituer_auteur($id_auteur, $couples);
	
	return true;
}

?>
