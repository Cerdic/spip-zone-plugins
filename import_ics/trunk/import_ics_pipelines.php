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
	// supprimer les almanachs anciens à la poubelle
	sql_delete('spip_almanachs', "statut='poubelle' AND maj < ".$flux['args']['date']);

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('almanach'=>'*'),'*');
	$flux['data'] += objet_optimiser_liens(array('mot'=>'*'), array('almanach' => '*'));

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


/**
 * Synchroniser le statut des evenements lorsqu'on publie/depublie un almanach
 * @param array $flux
 * @return array
 */
function import_ics_post_edition($flux) {
	if (isset($flux['args']['table'])
		and $flux['args']['table']=='spip_almanachs'
		and $flux['args']['action'] == 'instituer'
		and $id_almanach = $flux['args']['id_objet']
		and isset($flux['data']['statut'])
		and $statut = $flux['data']['statut']
		and $statut != $statut_ancien) {
		$set = array();
		switch ($statut) {
			case 'poubelle':
				// on passe aussi tous les evenements associes a la poubelle, sans distinction
				$set['statut'] = 'poubelle';
				break;
			case 'publie':
				// on passe aussi tous les evenements prop en publie
				$set['statut'] = 'publie';
				$where[] = "statut='prop'";
				break;
			case 'prop':
				$set['statut'] = 'prop';
				$where[] = "statut='publie'";
				break;
			}
		if (count($set)) {
			include_spip('action/editer_evenement');
			$res = sql_select('E.id_evenement', 'spip_evenements AS E
			INNER JOIN spip_almanachs_liens AS L
			ON E.id_evenement = L.id_objet AND L.id_almanach='.intval($id_almanach), $where);
			// et on applique a tous les evenements lies a l'almanach
			while ($row = sql_fetch($res)) {
				evenement_modifier($row['id_evenement'], $set);
			}
			sql_free($res);
		}
	}
	return $flux;
}
