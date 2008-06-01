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



function openid_log($data){
	if (_OPENID_LOG) spip_log('OpenID: '.$data, 'openid');
}



function demander_authentification_openid($login, $cible){
	openid_log("Traitement login OpenID");

	// Begin the OpenID authentication process.
	$consumer = init_auth_openid();
	$auth_request = $consumer->begin($login);

	// Handle failure status return values.
	if (!$auth_request) {
		// ici, on peut rentrer dire que l'openid n'est pas connu...
		// plutot que de rediriger et passer la main a d'autres methodes d'auth
		return _T('authopenid:erreur_openid');
		
		#include_spip('inc/cookie');
		#spip_setcookie("spip_admin", "", time() - 3600);
		#$redirect = openid_url_erreur(_T('authopenid:erreur_openid'));
	} 
	
	// l'openid donne est connu. On va donc envoyer une redirection
	// mais celle ci est differente selon la version de openid
	//
	// Dans les 2 cas, deux parametres doivent etre donnes
	// - une url de confiance, ici l'adresse du site : url_de_base()
	// - une url de redirection, sur laquelle OPENID reviendra une fois l'authentification faite (réussie ou non)
	else {
		// argument de redirection : cette url doit etre identique
		// ici et au retour (au moins le premier parametre de l'url)
		// sinon le script openid n'est pas content
		// On peut neanmoins passer des informations supplementaires
		// nous indiquons ici une autre redirection encore, celle de l'url
		// vers laquelle le bonhomme souhaite aller (url=$cible)
		
		// attention, il ne faut utiliser parametre_url() afin
		// d'encoderer $cible et casserait la transaction...
		$retour = parametre_url(openid_url_reception(), "url", url_absolue($cible), '&');

		// on demande quelques informations, dont le login obligatoire
		if ($sreg_request = Auth_OpenID_SRegRequest::build(
				array('nickname'), // Required
				array('fullname', 'email')) // Optional
  		) {
        	$auth_request->addExtension($sreg_request);
		}

		// OPENID 1
		if ($auth_request->shouldSendRedirect()) {
			// Redirect the user to the OpenID server for authentication.  Store
			// the token for this authentication so we can verify the response.
			$redirect = $auth_request->redirectURL(url_de_base(), $retour);		
			
			// If the redirect URL can't be built, display an error message.
			if (Auth_OpenID::isFailure($redirect)) {
				$redirect = openid_url_erreur(_L("Could not redirect to server: " . $redirect->message));
			}
		}
		
		// OPENID 2
		// use a Javascript form to send a POST request to the server.
		else {
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $auth_request->formMarkup(url_de_base(), $retour, false, array('id' => $form_id));

			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html)) {
				$redirect = openid_url_erreur(_L("Could not redirect to server: " . $form_html->message));
			} 
			
			// pas d'erreur : affichage du formulaire et arret du script
			else {
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
	include_spip('inc/headers');
	redirige_par_entete($redirect);		
}



// analyse le retour de la requete openID
// et redirige vers une url en fonction
function terminer_authentification_openid($redirect){

	// Complete the authentication process using the server's response.
	include_spip('inc/openid');
	$consumer = init_auth_openid();
	$response = $consumer->complete(openid_url_reception());

	// This means the authentication was cancelled.	
	if ($response->status == Auth_OpenID_CANCEL) {
	    $redirect = openid_url_erreur(_T('authopenid:verif_refusee')); 
	} 
	
	// Authentification echouee
	elseif ($response->status == Auth_OpenID_FAILURE) {
	    $redirect = openid_url_erreur("Authentication failed: " . $response->message);
	} 
	
	// This means the authentication succeeded.
	elseif ($response->status == Auth_OpenID_SUCCESS) {
	    $openid = $response->identity_url;
	    $esc_identity = htmlspecialchars($openid, ENT_QUOTES);
	    openid_log("Successful auth of $openid");

		// identification dans SPIP
		// (charge inc/auth_openid)
		$identifier_login = charger_fonction('identifier_login','inc');
		if (!$ok = $identifier_login($openid, "")){ // pas de mot de passe
			// c'est ici que l'on peut ajouter un utilisateur inconnu dans SPIP
			// en plus, on connait (si la personne l'a autorise) son nom et email
			// en plus du login
		
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

			openid_ajouter_auteur($couples);
			$ok = $identifier_login($openid, "");
			#$redirect = openid_url_erreur(_L("Echec de l'auth openid dans SPIP (utilisateur inconnu ?)"));
		}
		
		// sinon, c'est on est habilite ;)
		if ($ok) {
			$auth = charger_fonction('auth','inc');
			$auth();
			
			// Si on est admin, poser le cookie de correspondance
			if ($GLOBALS['auteur_session']['statut'] == '0minirezo') {
				include_spip('inc/cookie');
				spip_setcookie('spip_admin', '@'.$GLOBALS['auteur_session']['login'],
				time() + 7 * 24 * 3600);
			}
		}
	}
	
	include_spip('inc/headers');
	redirige_par_entete($redirect);	
}


function openid_url_reception(){
	include_spip('inc/filtres');
	return url_absolue(generer_url_action("controler_openid"));
}

function openid_url_erreur($message){
	openid_log($message);
	return parametre_url(
			generer_url_public("login","var_erreur=openid&url=".$redirect,'&'),
			"message_erreur", 
			urlencode($message));
}


function openid_ajouter_auteur($couples){
	$statut = ($GLOBALS['openid_statut_nouvel_auteur'] 
			? $GLOBALS['openid_statut_nouvel_auteur'] 
			: '1comite');
			
	include_spip('base/abstract_sql');
	$id_auteur = sql_insertq("spip_auteurs", array('statut' => $statut));
	
	include_spip('action/editer_auteur');
	include_spip('inc/modifier');
	instituer_auteur($id_auteur, $couples);
	
	return true;
}

?>
