<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Nettoyage journalier des fichiers obsoletes
 *
 * @param timestamp $last
 */
function genie_saveauto_cleaner_dist($last) {
	// On supprime les fichiers obsoletes en fonction de la duree de conservation
	$cleaner = charger_fonction('saveauto_cleaner','inc');
	$cleaner(array('auteur' => 'cron', 'manuel' => false));

	return 1;
}

