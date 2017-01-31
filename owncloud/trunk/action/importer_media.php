<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/documents');
include_spip('owncloud_fonctions');

/**
 * Importer les médias dans SPIP
 * 
 * @param string $arg l'URL cible
 * @return string
 */
function action_importer_media_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$ajouts = importer_media_owncloud($arg);

	return false;
}
