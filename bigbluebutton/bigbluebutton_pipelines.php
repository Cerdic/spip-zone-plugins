<?php

function bigbluebutton_header_prive($flux) {
	// On ajoute un CSS pour le back-office
	$flux .= "<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_BIGBLUEBUTTON."css/styles.css\" />";
	return $flux;
}

function bigbluebutton_header_public($flux){
        $csspublic = find_in_path('css/styles.css');
        $flux .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$csspublic."\" />";
        return $flux;
}

function bigbluebutton_taches_generales_cron($taches_generales){
        $taches_generales['bigbluebutton_automatisation_deleteroom'] = 60 * 10;
	return $taches_generales;
}

?>