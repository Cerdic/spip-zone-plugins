<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * On se greffe au pipeline taches_generales_cron pour lancer nos tâches
 * 
 * @param  array $taches
 * 
 * @return array
 */
function medias_nettoyage_taches_generales_cron($taches) {

	$taches['medias_deplacer_orphelins'] 	= 5 * 3600; // toutes les 5h
	$taches['medias_deplacer_obsoletes'] 	= 5 * 3600; // toutes les 5h
	$taches['medias_reparer_documents'] 	= 5 * 3600; // toutes les 5h

	return $taches;
}

/**
 * Ajouter des éléments dans le header du privé.
 * 
 * @param  string $flux
 * @return string
 */
function medias_nettoyage_header_prive($flux){

	$page_exec = array('medias_rep_orphelins', 'medias_rep_img', 'medias_tabbord');

	if (intval(spip_version()) == 2 AND in_array(_request('exec'), $page_exec)) {
		$flux .= '<link rel="stylesheet" href="' . find_in_path('prive/style_prive_medias_nettoyage.css') . '" type="text/css" media="all" />';
	}

	return $flux;
}

?>