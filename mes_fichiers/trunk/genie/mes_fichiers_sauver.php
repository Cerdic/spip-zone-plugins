<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Génération d'une sauvegarde par le cron
 *
 * @param timestamp $last
 */
function genie_mes_fichiers_sauver_dist($last) {
	// On lance la sauvegarde reguliere avec comme auteur le CRON
	$sauver = charger_fonction('mes_fichiers_sauver', 'inc');
	$sauver(null, array('auteur' => 'cron'));

	return 1;
}

?>
