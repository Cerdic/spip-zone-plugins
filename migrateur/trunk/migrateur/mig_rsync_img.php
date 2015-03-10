<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Synchroniser le répertoire IMG de destination avec la source
**/
function migrateur_mig_rsync_img() {
	$source = migrateur_source();
	$dest   = migrateur_destination();

	$dir_source = $source->dir  . 'IMG/';
	$dir_dest   = $dest->dir . 'IMG';

	$cmd = $dest->commande('rsync');
	if ($cmd) {
		$cmd = "$cmd -a -O --delete --stats";

		// source et destination sur serveurs différents
		if ($ssh = $source->ssh) {
			$dir_source = $source->ssh->obtenir_rysnc_parametres() . $dir_source;
		}

		$cmd = "$cmd $dir_source $dir_dest 2>&1";
		#migrateur_log($cmd);
		exec($cmd, $output, $err);
		migrateur_log(implode("\n", $output));

	}
}
