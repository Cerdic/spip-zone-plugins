<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Activation des sauvegardes regulieres si celles-ci ont ete activees
 * Par défaut une fois par jour (peut être modifié dans la conf)
 * Activiation du nettoyage journalier si activé
 *
 * @param array $taches_generales
 */
function mes_fichiers_taches_generales_cron($taches_generales){
	include_spip('inc/config');

	$sauver_auto = (lire_config('mes_fichiers/sauvegarde_reguliere', 'non') == 'oui');
	if ($sauver_auto) {
		$jour = lire_config('mes_fichiers/frequence', 1);
		$taches_generales['mes_fichiers_sauver'] = $jour*24*3600;
	}

	$laver_auto = (lire_config('mes_fichiers/nettoyage_journalier', 'oui') == 'oui');
	if ($laver_auto) {
		$taches_generales['mes_fichiers_cleaner'] = 24*3600;
	}

	return $taches_generales;
}

?>
