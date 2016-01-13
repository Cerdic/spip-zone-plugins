<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2010 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
		. "<br />[<a href=\"#\" onclick=\"jQuery('.editer_session .explication').hide();toggle_password(true);return false;\">"._T('openid:form_login_openid_pass')."</a>]";
		$scriptopenid = "jQuery('#var_login').keyup(function(){
			if (jQuery(this).val()!='".addslashes($login)."') {
				jQuery('.editer_session .explication').hide();
				toggle_password(true);
			} else {
				jQuery('.editer_session .explication').show();
			}
		});";
	}
	else
		$message = _T('openid:form_login_openid');

	// pas de required sur password
	$texte = preg_replace(",(<input[^>]*id='password'[^>]*)required='required'([^>]*/>),Uims","$1$2",$texte);

	$texte .= "<style type='text/css'><!--"
	."input#var_login {width:10em;background-image : url(".find_in_path('images/openid-16.png').");background-repeat:no-repeat;background-position:center left;padding-left:18px !important;}\n"
	."input#password {width:10em;padding-right:18px;}\n"
	.".editer_session .explication {margin:-5px 0 10px;font-style:italic;}"
	."//--></style>"
	."<script type='text/javascript'>"
	."/*<![CDATA[*/
	jQuery('#var_login').parents('form').addClass('openid');"
	."var memopass='';"
	."function toggle_password(show){
if (show) {
	if (memopass)
		jQuery('#password_holder').before(memopass);
	jQuery('#password_holder').remove();
	memopass = '';
	jQuery('.editer_password').show();
}
else {
	jQuery('#password').after('<span id=\"password_holder\"></span>');
	memopass = jQuery('#password').detach();
	jQuery('.editer_password').hide();
}
};"
	."jQuery(function(){jQuery('.editer_session').prepend('<div class=\'explication\'>".addslashes($message)."</div>');"
	.($scriptopenid?"if (!jQuery('.editer_password').is('.erreur')) toggle_password(false);":"")
	."$scriptopenid});"
	."/*]]>*/"
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
	if (!is_openid($url_openid))
		return false;
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
	// libs
	@define('_DIR_LIB', _DIR_RACINE . 'lib/');
	// assurer l'upgrade lorsque seule l'ancienne lib est encore presente
	$lib_dirs = array('openid-php-openid-ee669c6/','openid-php-openid-782224d/','php-openid-2.1.3');
	while (count($lib_dirs) AND !is_dir($f = _DIR_LIB . array_shift($lib_dirs)));
	@define('_DIR_OPENID_LIB', $f);
	@define('Auth_OpenID_RAND_SOURCE', null); // a priori...

	session_start();
	
	$cwd = getcwd();
	chdir(_DIR_OPENID_LIB);
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
				array('fullname', 'email', 'postcode', 'country', 'dob', 'gender')) // Optional
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
				$erreur = _L("Could not redirect to server: " . $redirect->message);
			}
			// pas d'erreur : redirection par entete
			else {
				openid_log("Redirection par entete", 3);
				include_spip('inc/headers');
				echo "<div class='formulaire_spip'>"
				. redirige_formulaire($redirect)
				. "</div>";
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
				$erreur = _L("Could not redirect to server: " . $form_html->message);
			} 
			
			// pas d'erreur : affichage du formulaire et arret du script
			else {
				openid_log("Affichage du formulaire de redirection", 3);
				echo openid_redirige_post($form_html,$form_id);
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
 * Redirection en post : il faut passer par un formulaire que l'on soumet
 * en javascript, en distinguant le cas ou l'on a ete poste en ajax et il ne
 * faut pas renvoyer une page html complete
 *
 * @param string $form
 * @param string $id
 * @return string
 */
function openid_redirige_post($form,$id){
	$out = "";
	if (!_AJAX
	  AND !headers_sent()
	  AND !_request('var_ajax')) {
		$out =
		 "<html><head><title>"
		 . "OpenID transaction in progress"
		 . "</title></head>"
		 . "<body onload='document.getElementById(\"".$id."\").submit()'>"
		 . _T('navigateur_pas_redirige')
		 . $form
		 . "</body></html>";
	}
	else {
		$out =
		  "<div class='formulaire_spip'>"
		  . _T('navigateur_pas_redirige')
		  . $form
		  . "</div>"
			. "<script type='text/javascript'>"
		  . "if (window.jQuery) jQuery(document).ready(function(){jQuery('#$id').animeajax().get(0).submit();});"
		  .	"</script>";
	}
	return $out;
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
		$identite = pipeline('openid_recuperer_identite',
			array('args' => $sreg,'data' => $identite)
		);
		return $identite;
	}
	return false;
}

/**
 * Fournir une url de retour pour l'inscription par OpenID
 * pour finir l'inscription
 *
 * @param string $idurl
 * @param string $redirect
 * @return string
 */
function openid_url_retour_insc($idurl, $redirect=''){
	$securiser_action = charger_fonction('securiser_action','inc');
	return $securiser_action('inscrire_openid', $idurl, $redirect, true);
}

?>