<?php


/**
 * La fonction à exécuter par le cron
 * @param unknown_type $last
 */
function genie_mes_fichiers_dist($last) {
	$sauver = charger_fonction('mes_fichiers_sauver','inc');
	$erreur = $sauver(null,array('auteur' => 'cron'));

	return 1;
}

/**
 * On s'insère dans le cron de SPIP
 * Par défaut une fois par jour (peut être modifié dans la conf)
 *
 * @param array $taches_generales
 */
function mes_fichiers_taches_generales_cron($taches_generales){
	$cfg = @unserialize($GLOBALS['meta']['mes_fichiers']);
	if (isset($cfg['sauvegarde_reguliere']) && ($cfg['sauvegarde_reguliere'] === 'true')){
		$jour = $cfg['frequence'] ? $cfg['frequence'] : 1;
		$taches_generales['mes_fichiers'] = $jour*24*3600;
	}
	return $taches_generales;
}

?>