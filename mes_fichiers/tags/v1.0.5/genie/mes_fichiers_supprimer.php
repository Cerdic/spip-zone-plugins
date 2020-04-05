<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Suppression des sauvegardes obsolètes
 *
 * @param timestamp $last
 */
function genie_mes_fichiers_supprimer_dist($last) {
	$supprimer_obsoletes = charger_fonction('mes_fichiers_cleaner','inc');
	$erreur = $supprimer_obsoletes(array('auteur' => 'cron'));

	return 1;
}

?>