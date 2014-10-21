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

		// source et destination sur serveurs différents
		if ($ssh = migrateur_source_ssh()) {
			exec("$cmd -a --no-o --no-p --delete --stats -e 'ssh -p {$ssh->port}' {$ssh->user}@{$ssh->server}:$source $dest 2>&1", $output, $err);
			migrateur_log(implode("\n", $output));
		}

		// source et destination sur le meme serveur
		else {
			exec("$cmd -a --delete --stats $source $dest 2>&1", $output, $err);
			migrateur_log(implode("\n", $output));
		}
	}
}
