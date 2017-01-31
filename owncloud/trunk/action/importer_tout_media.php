<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/documents');
include_spip('owncloud_fonctions');

/**
 * Importer tous les médias dans SPIP
 * 
 * @param string $arg l'URL cible
 * @return string
 */
function action_importer_tout_media_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$url = construire_url();

	include_spip('inc/flock');
	$lire_fichier = lire_fichier(_DIR_TMP . 'owncloud.json', $contenu);
	$lire_json = json_decode($contenu, true);
	foreach ($lire_json as $cle => $valeur) {
		$url_propre = securise_identifiants($valeur['document'], true);
		$ajouts = importer_media_owncloud($url_propre . '?' . $valeur['md5']);
	}

	return false;
}
