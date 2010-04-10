<?php


/**
 * La fonction à exécuter par le cron
 * @param unknown_type $last
 */
function genie_saveauto_dist($last) {
	$saveauto_creation = $GLOBALS['meta']['saveauto_creation'] ? $GLOBALS['meta']['saveauto_creation'] : 0;
	if ($GLOBALS['meta']['derniere_modif'] > $saveauto_creation){
		$saveauto = charger_fonction('saveauto','inc');
		$saveauto();
	}
}

/**
 * On s'insère dans le cron de SPIP
 * Par défaut une fois par jour (peut être modifié dans la conf)
 *
 * @param array $taches_generales
 */
function saveauto_taches_generales_cron($taches_generales){
	if ($cfg = @unserialize($GLOBALS['meta']['saveauto'])){
		$taches_generales['saveauto'] = $cfg['frequence_maj']*24*3600;
	}else{
		$taches_generales['saveauto'] = 24*3600;
	}
	return $taches_generales;
}

?>