<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('owncloud_fonctions');

/**
 * Purger les médias dans SPIP
 * 
 * @param string $arg l'URL cible
 * @return string
 */
function action_purger_media_dist() {

	$purger = purger_media_spip();

	return $purger;
}
