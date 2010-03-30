<?php

//
// Alerte sur les articles publies post-dates
//

function genie_saveauto_dist($last) {
	$saveauto_creation = $GLOBALS['meta']['saveauto_creation'] ? $GLOBALS['meta']['saveauto_creation'] : time();
	if ($GLOBALS['meta']['derniere_modif'] > $saveauto_creation){
		$saveauto = charger_fonction('saveauto','inc');
		$saveauto();
	}
	return 1;
}

function saveauto_taches_generales_cron($taches_generales){
	if ($cfg = @unserialize($GLOBALS['meta']['saveauto'])){
		$taches_generales['saveauto'] = $cfg['frequence_maj']*24*3600;
	}else{
		$taches_generales['saveauto'] = 24*3600;
	}
	return $taches_generales;
}

?>