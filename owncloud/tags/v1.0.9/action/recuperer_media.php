<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('owncloud_fonctions');
include_spip('inc/distant');

/**
 * Récupérer les médias dans un fichier json à la racine de tmp/
 * 
 * @param string $arg l'URL cible
 * @return string
 */
function action_recuperer_media_dist() {

	include_spip('inc/config');
	$config = lire_config('owncloud');

	$url = construire_url();

	include_spip('lib/SabreDAV/vendor/autoload');

	$settings = array(
		'baseUri' => $url['url'],
		'userName' => $config['login'],
		'password' => $config['password']
	);

	try {
		$client = new Sabre\DAV\Client($settings);
		$liste = $client->propfind($settings['baseUri'], array('{DAV:}displayname', '{DAV:}getcontentlength', '{DAV:}getlastmodified'), 1);
	} catch (Exception $e) {
		$code = $e->getMessage();
		if ($code) {
			$erreur = 'oui';
		}
		return $erreur;
	}

	if (!in_array($code, array('401', '404', '405', '501')) || !$code) {
		$fichiers=array();
		foreach ($liste as $cle => $valeur) {
			$document = $url['url_courte'].$cle;

			$md5 = md5(basename($document).$valeur['{DAV:}getlastmodified'].$valeur['{DAV:}getcontentlength']);

			if (function_exists('curl_init')) {
				$body = recuperer_infos_distantes_curl($document);
			} else {
				$body = recuperer_infos_distantes($document);
			}
			
			if (preg_match('/(image|application|text)/', $body['mime_type']) && isset($valeur['{DAV:}getcontentlength'])) {
				$document = securise_identifiants($document);

				array_push(
								$fichiers,
								array(
									'nom' => $cle,
									'document' => $document,
									'md5' => $md5,
									'body' => array(
										'extension' => $body['extension'],
										'taille' => $body['taille'],
										'fichier' => $body['fichier'],
										'largeur' => $body['largeur'],
										'hauteur' => $body['hauteur'],
										'type_image' => $body['type_image'],
										'mime_type' => $body['mime_type']),
									'getlastmodified' => $valeur['{DAV:}getlastmodified'])
				);
			}

		}

		$json = json_encode($fichiers, true);
		include_spip('inc/flock');
		ecrire_fichier(_DIR_TMP . 'owncloud.json', $json);

		return $fichiers;

	}

}
