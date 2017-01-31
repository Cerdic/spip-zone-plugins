<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Tester la connexion a owncloud
 *
 * @return string Message d'erreur
 *
 **/
function test_connexion_dav() {

	include_spip('owncloud_fonctions');
	include_spip('inc/config');
	$config = lire_config('owncloud');

	$url = construire_url();

	include_spip('lib/SabreDAV/vendor/autoload');

	$settings = array(
		'baseUri' => $url['url'],
		'userName' => $config['login'],
		'password' => $config['password']
	);

	if ($settings['baseUri']) {
		try {
			$client = new Sabre\DAV\Client($settings);
			$liste = $client->request('GET', $settings['baseUri']);
		} catch (Exception $e) {
			$code = $e->getMessage();
		}

		if (in_array($liste['statusCode'], array('401', '404', '405', '501')) || $code) {
			$message_auth = 'non';
		} else {
			$message_auth = 'oui';
		}
	} else {
			$message_auth = 'non';
	}

	return $message = array('message_auth' => $message_auth, 'code' => $code);
}
