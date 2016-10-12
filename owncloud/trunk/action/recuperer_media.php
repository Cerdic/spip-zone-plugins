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

	$url = construire_url();

	include_spip('lib/SabreDAV/vendor/autoload');

	$settings = array(
		'baseUri' => $url['url']
	);

	try {
		$client = new Sabre\DAV\Client($settings);
		$liste = $client->propfind($settings['baseUri'], array('{DAV:}displayname', '{DAV:}getcontentlength', '{DAV:}getlastmodified'), 1);
	} catch (Exception $e) {
		$code = $e->getHttpStatus();
	}

	if ($code != 404) {

		$fichiers=array();
		foreach ($liste as $cle => $valeur) {
			$document = $url['url_courte'].$cle;

			$md5 = md5(basename($document).$valeur['{DAV:}getlastmodified'].$valeur['{DAV:}getcontentlength']);

			if (function_exists('curl_init')) {
				$body = curl_get($document, false, true, 10, false, '');
				
				$pathinfo = pathinfo($document);
				$fichier = $pathinfo['basename'];
				$extension = strtolower($pathinfo['extension']);

				$fichier = _DIR_RACINE . nom_fichier_copie_locale($document, $extension);
				ecrire_fichier($fichier, $body);
				$size_image = @getimagesize($fichier);
				$taille = filesize($fichier);
				$type_image = true;

				$body = array(
					'extension' => $extension,
					'taille' => $taille,
					'fichier' => $fichier,
					'largeur' => intval($size_image[0]),
					'hauteur' => intval($size_image[1]),
					'type_image' => $type_image,
					'mime_type' => $size_image['mime']);
			} else {
				$body = recuperer_infos_distantes($document);
			}
			
			if (preg_match('/(image|application|text)/', $body['mime_type']) && isset($valeur['{DAV:}getcontentlength'])) {
				$document = securise_identifiants($document);

				array_push($fichiers,
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

	} else {
		$fichiers = array(array('nom' => $code));
	}
		
	$json = json_encode($fichiers, true);
	include_spip('inc/flock');
	ecrire_fichier(_DIR_TMP . 'owncloud.json', $json);

	return $fichiers;

}
