<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Synchroniser le répertoire IMG de destination avec la source
**/
function migrateur_mig_rsync_img() {
	$source     = MIGRATEUR_SOURCE_DIR  . 'IMG/';
	$dest       = MIGRATEUR_DESTINATION_DIR . 'IMG';
	exec("rsync -a --delete --stats $source $dest", $output, $err);
	migrateur_log(implode("\n", $output));
}
