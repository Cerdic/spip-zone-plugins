<?php
/**
 * Ce fichier contient les fonctions permettant de poster
 * des éléments sur facebook
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Poster un lien sur le profil de la personne ou sur la page définie par la configuration
 *
 * @param string $id_page
 * @param string $lien
 * @param string $message
 * @param string $token
 * @access public
 * @return string Token ou erreur facebook
 */
function facebook_poster_lien_page($lien, $message, $id_page = 'me', $token = false) {

	if (!$token) {
		include_spip('inc/token');
		$token = connecteur_get_token(0, 'facebook');
	}

	$fb = facebook();

	$linkData = [
		'link' => $lien,
		'message' => $message,
	];

	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->post('/'.$id_page.'/feed', $linkData, $token);
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		return 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		return 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
}
