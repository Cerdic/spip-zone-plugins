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
 * Optimiser la base de données en supprimant 
 * les almanachs à la poubelle
 * et les liens orphelins depuis et vers les almanachs
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function import_ics_optimiser_base_disparus($flux){

	include_spip('action/editer_liens');

	$res = sql_select(
		'id_almanach AS id',
		'spip_almanachs',
		'statut='.sql_quote('poubelle')
	);
	
	$flux['data'] += optimiser_sansref('spip_almanachs', 'id_almanach', $res);
	$flux['data'] += objet_optimiser_liens(array('almanach'=>'*'),'*');
	$flux['data'] += objet_optimiser_liens(array('mot'=>'*'), array('almanach' => '*'));

	return $flux;
}



function import_ics_taches_generales_cron($taches_generales){
	$taches_generales['import_ics_synchro'] = 3600*1.5;/*mettre à jour toutes les 1h/30 parait bien*/
	return $taches_generales;
}

function import_ics_evenement_liaisons_colonne_gauche($flux){
	$flux["data"]= $flux["data"].recuperer_fond("prive/objets/infos/evenement_liaisons_almanach",$flux['args']);
	return $flux;
}


/**
 * Synchroniser le statut des evenements lorsqu'on publie/depublie un almanach
 * Synchroniser le statut des almanaches lorsqu'on publie/depublie un evt
 * @param array $flux
 * @return array
 */
function import_ics_post_edition($flux) {
	/* Première cas: le statut de l'almanach est modifié*/
	if (isset($flux['args']['table'])
		and $flux['args']['table']=='spip_almanachs'
		and $flux['args']['action'] == 'instituer'
		and $id_almanach = $flux['args']['id_objet']
		and isset($flux['data']['statut'])
		and $statut = $flux['data']['statut']
		and $statut_ancien = $flux['args']['statut_ancien']
		and $statut != $statut_ancien) {
		$set = array();
		$where = array();
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
	// Second cas: modification du statut d'un article
	
	if (isset($flux['args']['table'])
		and $flux['args']['table']=='spip_articles'
		and $flux['args']['action'] == 'instituer'
		and $id_article = $flux['args']['id_objet']
		and isset($flux['data']['statut'])
		and $statut = $flux['data']['statut']
		and $statut_ancien = $flux['args']['statut_ancien']
		and $statut != $statut_ancien) {
		$set = array();
		// les almanachs associes a cet article
		$where = array('id_article='.intval($id_article));
		switch ($statut) {
			case 'poubelle':
				// on passe aussi tous les almanachs associes a la poubelle, sans distinction
				$set['statut'] = 'poubelle';
				break;
			case 'publie':
				// on passe aussi tous les almanachs prop en publie
				$set['statut'] = 'publie';
				$where[] = "statut='prop'";
				break;
			default:// c'est à dire lors de la bascule de l'article vers refuse / en cours de modification / proposé
				if ($statut_ancien=='publie') {
					// on depublie aussi tous les almanachs publie
					$set['statut'] = 'prop';
					$where[] = "statut='publie'";
				}
				break;
		}
		if (count($set)) {
			include_spip('inc/autoriser');
			include_spip('action/editer_objet');
			$res = sql_select('id_almanach', 'spip_almanachs', $where);
			// et on applique a tous les evenements lies a l'article
			while ($row = sql_fetch($res)) {
				$id_almanach = $row['id_almanach'];
				autoriser_exception('instituer','almanach',$id_almanach);
				objet_instituer('almanach',$id_almanach,$set);
				autoriser_exception('instituer','almanach',$id_almanach,false);
			}
		}
	}
	
	return $flux;
}


/**
 * Inserer les infos d'almanach sur les articles
 *
 * @param array $flux
 * @return array
 */
function import_ics_affiche_milieu($flux){
	$e = trouver_objet_exec($flux['args']['exec']);
	$out = False;
	if ($e["type"]=="article" and $e['edition']==false){
		$out = recuperer_fond('prive/objets/contenu/article-almanachs', $flux['args']);
		
	}
	
	if ($out) {
		if ($p = strpos($flux['data'],'<div id="agenda">')){
			$flux['data'] = substr_replace($flux['data'], $out, $p, 0);
		}
		elseif ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $out, $p, 0);
		} else {
			$flux['data'] .= $out;
		}
	}
	return $flux;
}

/**
* Modifier les mots-clé liés à un évènement 
* lorsque l'almanach de cet évènement voit ses mot-clé modifiés
* @param array $flux
* @return array
*/
function import_ics_post_edition_lien($flux){
	if ($flux['args']['objet_source'] == 'mot' and $flux['args']['objet'] == 'almanach'){//si on modifie la liaison mot/objet
		// ce qui a été modifié
		$id_mot = $flux['args']['id_objet_source'];
		$id_almanach = $flux['args']['id_objet'];
		$action = $flux['args']['action'];
		
		// chercher les evenement liés à l'almanach
		include_spip('inc/import_ics');
		$evenements = trouver_evenements_almanach($id_almanach,'id_evenement',true);
		foreach ($evenements as $evt){
			$id_evenement = $evt['id_evenement'];
			if ($action == 'delete'){
				objet_dissocier(
					array('mot'=>$id_mot),
					array('evenement'=>$id_evenement)
				);
				spip_log ("Dissociation du mot-clef $id_mot de l'évènement $id_evenement suite à modif de l'almanach $id_almanach","import_ics"._LOG_INFO);
			}
			elseif ($action == 'insert'){
				objet_associer(
					array('mot'=>$id_mot),
					array('evenement'=>$id_evenement)
				);
				spip_log ("Association du mot-clef $id_mot de l'évènement $id_evenement suite à modif de l'almanach $id_almanach","import_ics"._LOG_INFO);					
			}
		}
	}
	return $flux;
}


/** Déclarer les almanachs
 * au plugin corbeille
 * @param array $flux;
 * @return array $flux;
**/
function import_ics_corbeille_table_infos($flux){
	$flux['almanachs']= array(
		'statut'=>'poubelle',
		'table'=>'almanachs', 
		'tableliee'=>array('spip_almanachs_liens')
	);
	return $flux;
}
