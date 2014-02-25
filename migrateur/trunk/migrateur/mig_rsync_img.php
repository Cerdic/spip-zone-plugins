<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Synchroniser le répertoire IMG de destination avec la source
**/
function migrateur_mig_rsync_img() {
	$source     = MIGRATEUR_SOURCE_DIR  . 'IMG/';
	$dest       = MIGRATEUR_DESTINATION_DIR . 'IMG';
	$cmd = migrateur_obtenir_commande_serveur('rsync');
	if ($cmd) {
		exec("$cmd -a --delete --stats $source $dest 2>&1", $output, $err);
		migrateur_log(implode("\n", $output));
	}
}
