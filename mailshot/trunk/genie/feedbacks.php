<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_CRON_LOT_FICHIERS_FEEDBACK')) {
	define('_CRON_LOT_FICHIERS_FEEDBACK', 1000);
}
if (!defined('_CRON_LOT_NB_FEEDBACKS')) {
	define('_CRON_LOT_NB_FEEDBACKS', 100);
}

/**
 * Cron de calcul de statistiques des visites
 *
 * Calcule les stats en plusieurs étapes
 *
 * @uses calculer_visites()
 *
 * @param int $t
 *     Timestamp de la dernière exécution de cette tâche
 * @return int
 *     Positif si la tâche a été terminée, négatif pour réexécuter cette tâche
 **/
function genie_feedbacks_dist($t) {

	include_spip('newsletter/feedback');

	// charger un certain nombre de fichiers de visites,
	// et faire les calculs correspondants

	// Traiter jusqu'a 100 lots de feedbacks datant d'au moins 1 minutes
	// (juste pour eviter de depouiller un fichier en cours d'ecriture)
	$fichiers_feedbacks = preg_files(sous_repertoire(_DIR_TMP, 'newsletter_feedbacks'));

	$feedbacks = array();
	$compteur = _CRON_LOT_FICHIERS_FEEDBACK;
	$date_init = time() - 60;
	foreach ($fichiers_feedbacks as $fichier) {
		if (($d = @filemtime($fichier)) < $date_init) {

			$feedbacks_content = file_get_contents($fichier);
			$feedbacks_content = explode("\n", $feedbacks_content);
			foreach ($feedbacks_content as $line) {
				if ($line and $f = json_decode($line,true)) {
					$feedbacks[] = $f;
				}
			}

			if (count($feedbacks)>_CRON_LOT_NB_FEEDBACKS) {
				newsletter_feedback_lot($feedbacks);
				$feedbacks = array();
			}

			spip_unlink($fichier);
			if (--$compteur <= 0) {
				break;
			}
		}
	}

	if (count($feedbacks)) {
		newsletter_feedback_lot($feedbacks);
	}

	// Si ce n'est pas fini on redemande la main
	// pour etre prioritaire lors du cron suivant
	if ($compteur<=0) {
		return (0 - $t);
	}

	return 1;
}
