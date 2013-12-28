<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Activation des sauvegardes regulieres si celles-ci ont ete activees
 * Par défaut une fois par jour (peut être modifié dans la conf)
 * Activation du nettoyage journalier si demandé
 *
 * @param array $taches_generales
 */
function saveauto_taches_generales_cron($taches_generales){
	include_spip('inc/config');

	$sauver_auto = (lire_config('saveauto/sauvegarde_reguliere') == 'oui');
	if ($sauver_auto) {
		$jour = lire_config('saveauto/frequence_maj');
		$taches_generales['saveauto'] = $jour*24*3600;
	}

	$laver_auto = (lire_config('saveauto/nettoyage_journalier') == 'oui');
	if ($laver_auto) {
		$taches_generales['saveauto_cleaner'] = 24*3600;
	}

	return $taches_generales;
}

?>
