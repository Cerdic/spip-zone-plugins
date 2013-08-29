<?php
/**
 * Plugin OCR
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function ocr_taches_generales_cron($taches_generales) {
	$ocr = @unserialize($GLOBALS['meta']['ocr']);
	if ($ocr['intervalle_cron']) {$taches_generales['ocr_analyse_document'] = $ocr['intervalle_cron'];
	} else {
		@define('_OCR_INTERVALLE_CRON',600); // toutes les 10 minutes
		$taches_generales['ocr_analyse_document'] = _OCR_INTERVALLE_CRON;
	}
	return $taches_generales;
}

function ocr_rechercher_liste_des_champs($tables){
	$tables['document']['ocr'] = 1;
	return $tables;
}

function ocr_rechercher_liste_des_jointures($tables){
	$tables['article']['document']['ocr'] = 1;
	$tables['rubrique']['document']['ocr'] = 1;
	$tables['breve']['document']['ocr'] = 1;
	spip_log($tables ,'ocr');
	return $tables;
}

?>
