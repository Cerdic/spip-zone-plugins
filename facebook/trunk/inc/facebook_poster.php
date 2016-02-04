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
 * Poster un lien sur Le profil de la personne
 *
 * @param string $lien
 * @param string $message
 * @access public
 * @return string Un message d'erreur au besoin
 */
function facebook_poster_lien($lien, $message) {

	include_spip('inc/config');
	$config = lire_config('facebook');

	$fb = facebook();

	$linkData = [
		'link' => $lien,
		'message' => $message,
	];

	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->post('/me/feed', $linkData, $config['accessToken']);
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		return 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		return 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
}

/**
 * Poster un lien sur la page définie par la configuration
 *
 * @access public
 * @return string Token ou erreur facebook
 */
function facebook_poster_lien_page($id_page, $lien, $message) {

	include_spip('inc/config');
	$config = lire_config('facebook');

	$fb = facebook();

	$linkData = [
		'link' => $lien,
		'message' => $message,
	];

	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->post('/'.$id_page.'/feed', $linkData, facebook_page_token($id_page));
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		return 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		return 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
}
