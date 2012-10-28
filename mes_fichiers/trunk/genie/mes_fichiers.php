<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Génération d'une sauvegarde par le cron et nettoyage des fichiers obsoletes dans la foulee
 *
 * @param timestamp $last
 */
function genie_mes_fichiers_dist($last) {
	// On lance la sauvegarde reguliere avec comme auteur le CRON
	$sauver = charger_fonction('mes_fichiers_sauver', 'inc');
	$sauver(null, array('auteur' => 'cron'));

	// On supprime les fichiers obsoletes en fonction de la duree de conservation
	$supprimer_obsoletes = charger_fonction('mes_fichiers_cleaner','inc');
	$supprimer_obsoletes(array('auteur' => 'cron'));

	return 1;
}

?>
