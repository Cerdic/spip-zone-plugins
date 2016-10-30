<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('lib/facebook-php-sdk/src/Facebook/autoload');
include_spip('inc/facebook_poster');

// Le SDK de facebook à besoin d'une session php mais n'est pas foutu de faire lui même ce test.
if (!session_id()) {
	session_start();
}

/**
 * Initialiser Facebook avec la configuration stockée dans SPIP
 *
 * @access public
 * @return object L'objet Facebook crée
 */
function facebook() {

	include_spip('inc/config');
	$config = lire_config('facebook');

	$fb = new Facebook\Facebook([
		'app_id' => $config['cle'],
		'app_secret' => $config['secret'],
		'default_graph_version' => 'v2.2'
	]);

	return $fb;
}

/**
 * Obtenir un lien de connection Facebook
 *
 * @access public
 * @param string $action Action sur lequel sera envoyer le tocken
 * @return string Lien vers Facebook
 */
function facebook_lien_connection($action) {

	include_spip('inc/config');
	$config = lire_config('facebook');

	// Si facebook n'est pas configurer, on n'affiche pas de lien
	if (empty($config['cle']) or empty($config['secret'])) {
		return false;
	}

	// Raccourcis pour les auteurs
	if ($action == 'auteur') {
		$action = 'facebook_access_token_auteur';
	}

	$fb = facebook();

	$helper = $fb->getRedirectLoginHelper();

	$permission = explode(',', _FACEBOOK_PERMISSION);

	$loginUrl = $helper->getLoginUrl($action, $permission);

	return htmlspecialchars($loginUrl);
}

/**
 * On récupère le token d'accès
 *
 * @access public
 * @return string Un message d'erreur au besoin
 */
function facebook_access_token() {

	// S'il n'y a pas de code, pas besoin de chercher un token
	if (!_request('code')) {
		return false;
	}

	$fb = facebook();

	include_spip('inc/config');
	$config = lire_config('facebook');

	$helper = $fb->getRedirectLoginHelper();

	try {
		$accessToken = $helper->getAccessToken();
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		return 'Graph returned an error: ' . $e->getMessage();
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		return 'Facebook SDK returned an error: ' . $e->getMessage();
	}

	if (! isset($accessToken)) {
		if ($helper->getError()) {
			header('HTTP/1.0 401 Unauthorized');
			echo 'Error: '.$helper->getError()."\n";
			echo 'Error Code: '.$helper->getErrorCode()."\n";
			echo 'Error Reason: '.$helper->getErrorReason()."\n";
			echo 'Error Description: '.$helper->getErrorDescription()."\n";
		} else {
			header('HTTP/1.0 400 Bad Request');
			echo 'Bad request';
		}
		exit;
	}

	// The OAuth 2.0 client handler helps us manage access tokens
	$oAuth2Client = $fb->getOAuth2Client();

	// Get the access token metadata from /debug_token
	$tokenMetadata = $oAuth2Client->debugToken($accessToken);

	// Validation (these will throw FacebookSDKException's when they fail)
	$tokenMetadata->validateAppId($config['cle']);
	// If you know the user ID this access token belongs to, you can validate it here
	//$tokenMetadata->validateUserId('123');
	$tokenMetadata->validateExpiration();

	if (! $accessToken->isLongLived()) {
		// Exchanges a short-lived access token for a long-lived one
		try {
			$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			return '<p>Error getting long-lived access token: '.$helper->getMessage()."</p>\n\n";
		}
	}

	return $accessToken;
}

/**
 * Lister les page accessible par l'utilisateur
 *
 * @access public
 * @return mixed
 */
function facebook_liste_pages() {

	$fb = facebook();

	include_spip('inc/token');
	$token = connecteur_get_token(0, 'facebook');

	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get('/me/accounts', $token);
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		return 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		return 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	// On récupère les éléments pages
	$graphEdges = $response->getGraphEdge();

	return $graphEdges;
}


/**
 * Récupérer le token d'une page
 *
 * @param int $id_page
 * @access public
 * @return mixed
 */
function facebook_page_token($id_page) {

	$graphEdges = facebook_liste_pages();
	foreach ($graphEdges as $graphEdge) {
		if ($graphEdge['id'] == $id_page) {
			return $graphEdge['access_token'];
		}
	}

	return _T('facebook:erreur_page_access_token');
}

/**
 * Créer une datas saisies à partir des pages de la personne
 *
 * @access public
 * @return array Datas pour saisies
 */
function facebook_saisie_pages() {

	$graphEdges = facebook_liste_pages();
	if (!is_string($graphEdges)) {
		// Replir un tableau utilisable avec saisies
		$datas = array();
		// par defaut, on place id du "me"
		$T_user = facebook_profil();
		$datas[$T_user['id']] = $T_user['nom'];
		foreach ($graphEdges as $graphEdge) {
			$datas[$graphEdge['id']] = 'Page : '.$graphEdge['name'];
		}
	} else {
		// C'est une erreur, on la renvoie
		return $graphEdges;
	}

	return $datas;
}

/**
 * Récupérer le profil facebook de la personne
 * Si aucun token n'est passé, ce sera la configuration du site qui sera utilisée
 *
 * @param mixed $token
 * @access public
 * @return array
 */
function facebook_profil($token = null) {

	$fb = facebook();

	if (empty($token)) {
		include_spip('inc/token');
		$token = connecteur_get_token(0, 'facebook');
	}

	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get('/me?fields='._FACEBOOK_CHAMP_PROFIL, $token);
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		return 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		return 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	$user = $response->getGraphUser();
	spip_log($user, 'facebook', _LOG_DEBUG);

	return array(
		'nom' => $user['name'],
		'id' => $user['id'],
		'email' => $user['email'],
		'facebook' => $user
	);
}

/**
 * Récupérer l'image de profil
 * Attention ! Il faut ajouter la permission Facebook `user_about_me`
 *
 * @param mixed $token
 * @param int $width
 * @param int $height
 * @access public
 * @return mixed
 */
function facebook_profil_picture($token = null, $width = 0, $height = 0) {

	$fb = facebook();

	if (empty($token)) {
		include_spip('inc/token');
		$token = connecteur_get_token(0, 'facebook');
	}

	try {
		$size = '';
		if ($width > 0) {
			$size .= '&width='.$width;
		}
		if ($height > 0) {
			$size .= '&height='.$height;
		}

		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get('/me/picture?redirect=false'.$size, $token);
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		return 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		return 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	$picture = $response->getGraphUser();

	return $picture;
}


/**
 * Recuperation de la liste des posts
 * Si aucun token n'est passé, ce sera la configuration du site qui sera utilisée
 *
 * @param mixed $token
 * @access public
 * @return array
 **/
function facebook_recup_posts($token = null) {

	$fb = facebook();
	if (empty($token)) {
		include_spip('inc/token');
		$token = connecteur_get_token(0, 'facebook');
	}
	$id_page = lire_config('facebook_compte_post');
	try {
		$response = $fb->get('/'.$id_page.'/posts?fields=message,picture,created_time', $token);
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		return 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		return 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	$graphObject = $response->getGraphEdge()->asArray();
	$T_result = array();
	foreach ($graphObject as $message) {
		foreach ($message['created_time'] as $k => $val) {
			if ($k == 'date') {
				$date_creation = $val;
			}
		}
		$T_result[] = array(
			'message' => $message['message'],
			'url_picture' => $message['picture'],
			'date_creation' => $date_creation
		);
	}

	return $T_result;
}
