<?php
/**
 * Utilisations de pipelines par Import_ics
 *
 * @plugin     Import_ics
 * @copyright  2013
 * @author     Amaury
 * @licence    GNU/GPL
 * @package    SPIP\Import_ics\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	
/**
 * Optimiser la base de données en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function import_ics_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('almanach'=>'*'),'*');
	return $flux;
}


function import_ics_taches_generales_cron($taches_generales){
	$taches_generales['import_ics_synchro'] = 3600*24;/*mettre à jour toutes les 24 heures parait bien*/
	return $taches_generales;
}

function import_ics_evenement_liaisons_colonne_gauche($flux){
	$flux["data"]= $flux["data"].recuperer_fond("prive/objets/infos/evenement_liaisons_almanach",$flux['args']);
	return $flux;
}
?>