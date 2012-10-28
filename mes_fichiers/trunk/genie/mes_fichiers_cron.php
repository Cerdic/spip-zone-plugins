<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * On s'insère dans le cron de SPIP si les sauvegardes regulieres ont ete activees
 * Par défaut une fois par jour (peut être modifié dans la conf)
 *
 * @param array $taches_generales
 */
function mes_fichiers_taches_generales_cron($taches_generales){
	include_spip('inc/config');

	$sauver_auto = (lire_config('mes_fichiers/sauvegarde_reguliere', 'non') == 'oui');

	if ($sauver_auto) {
		$jour = lire_config('mes_fichiers/frequence', 1);
		$taches_generales['mes_fichiers'] = $jour*24*3600;
	}

	return $taches_generales;
}

?>
