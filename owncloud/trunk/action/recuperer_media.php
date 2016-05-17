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

	$client = new Sabre\DAV\Client($settings);
	$liste = $client->propfind($settings['baseUri'], array('{DAV:}displayname', '{DAV:}getcontentlength', '{DAV:}getlastmodified'), 1);

	$fichiers=array();
	foreach ($liste as $cle => $valeur) {
		$document = $url['url_courte'].$cle;
		$md5 = md5(basename($document).$valeur['{DAV:}getlastmodified'].$valeur['{DAV:}getcontentlength']);
		$body = recuperer_infos_distantes($document);
		// TODO: Tester pour récupérer seulement des fichiers et non des répertoires
		if (preg_match('/(image|application|text)/', $body['mime_type'])) {
			$document = securise_identifiants($document);
			array_push($fichiers, array(
					'nom' => $cle,
					'document' => $document,
					'md5' => $md5,
					'body' => array(
						'extension' => $body['extension'],
						'taille' => $body['taille'],
						'fichier' => $body['fichier'],
						'largueur' => $body['largeur'],
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
