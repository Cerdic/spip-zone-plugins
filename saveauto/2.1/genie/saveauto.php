<?php


/**
 * La fonction à exécuter par le cron
 * On vérifie que la date de dernière modification du site soit supérieure
 * à la dernière sauvegarde
 * @param unknown_type $last
 */
function genie_saveauto_dist($last) {
	$saveauto_creation = $GLOBALS['meta']['saveauto_creation'] ? $GLOBALS['meta']['saveauto_creation'] : 0;
	$derniere_modif = max($GLOBALS['meta']['derniere_modif'],$GLOBALS['meta']['derniere_modif_rubrique'],$GLOBALS['meta']['derniere_modif_article'],$GLOBALS['meta']['derniere_modif_auteur'],$GLOBALS['meta']['derniere_modif_syndic'],$GLOBALS['meta']['derniere_modif_forum']);
	if ($derniere_modif > $saveauto_creation){
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
		//$taches_generales['saveauto'] = $cfg['frequence_maj']*60; #pour debug
	}else{
		$taches_generales['saveauto'] = 24*3600;
	}
	return $taches_generales;
}

?>