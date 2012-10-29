<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Nettoyage journalier des fichiers obsoletes
 *
 * @param timestamp $last
 */
function genie_mes_fichiers_cleaner_dist($last) {
	// On supprime les fichiers obsoletes en fonction de la duree de conservation
	$supprimer_obsoletes = charger_fonction('mes_fichiers_cleaner','inc');
	$supprimer_obsoletes(array('auteur' => 'cron'));

	return 1;
}

?>
