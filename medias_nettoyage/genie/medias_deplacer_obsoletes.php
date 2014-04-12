<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * On passe par un cron pour s'occuper des dossiers et répertoires obsolètes
 * On est sur une durée de 30 jours. Mais il faudrait trouver une astuce 
 * pour ne lancer le cron que s'il y a un répertoire ou fichier obsollète.
 * 
 * @param  unknown $t
 * 
 * @return bool
 */
function genie_medias_deplacer_obsoletes_dist ($t) {
	include_spip('medias_nettoyage_fonctions');

	if (function_exists('medias_deplacer_rep_obsoletes')) {
		medias_deplacer_rep_obsoletes();
	}


	return 1;
}
?>