<?php
function clevermail_taches_generales_cron($taches_generales) {
	$taches_generales['clevermail_queue_process'] = 60; // toutes les minutes
  $taches_generales['clevermail_automatisation'] = 60 * 60; // toutes les heures
  $taches_generales['clevermail_auto_ajout_abonnes'] = 60 * 60; // toutes les heures
  return $taches_generales;
}

function clevermail_header_prive($flux) {
	// On ajoute un CSS et un JS pour le back-office
	$flux .= "<script src=\""._DIR_PLUGIN_CLEVERMAIL."js/functions.js\" type=\"text/javascript\"></script>";
	$flux .= "<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_CLEVERMAIL."css/styles.css\" />";
	return $flux;
}
?>