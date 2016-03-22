<?php
/*
 * Plugin Polyhierarchie
 * (c) 2009-2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/rubriques');

/**
 * Mettre a jour les parents d'un objet
 *
 * @param int $id_objet
 * @param string $objet
 * @param array $id_parents
 * @param string $serveur
 * @return array
 */
function polyhier_set_parents($id_objet,$objet,$id_parents,$serveur=''){
	if (is_string($id_parents))
		$id_parents = explode(',',$id_parents);
	if (!is_array($id_parents))
		$id_parents = array();

	$id_parents = array_unique($id_parents);

	$changed = array('remove'=>array(),'add'=>array());

	$where = "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet);
	// supprimer les anciens parents plus utilises
	// en les notant auparavant
	$changed['remove'] = sql_allfetsel("id_parent","spip_rubriques_liens","$where AND ".sql_in('id_parent',$id_parents,"NOT",$serveur),$serveur);
	$changed['remove'] = array_map('reset',$changed['remove']);
	sql_delete("spip_rubriques_liens","$where AND ".sql_in('id_parent',$id_parents,"NOT",$serveur),$serveur);

	// selectionner l'intersection entre base et tableau
	$restants = sql_allfetsel("id_parent","spip_rubriques_liens","$where AND ".sql_in('id_parent',$id_parents,"",$serveur),"","","","",$serveur);
	$restants = array_map('reset',$restants);

	$id_parents = array_diff($id_parents,$restants);
	$ins = array();
	foreach($id_parents as $p){
		if ($p) {
			$ins[] = array('id_parent'=>$p,'id_objet'=>$id_objet,'objet'=>$objet);
			$changed['add'][] = $p;
		}
	}
	if (count($ins))
		sql_insertq_multi("spip_rubriques_liens",$ins,"",$serveur);

	return $changed;
}

/**
 *
 * @param int|array $id_objet
 * @param string $objet
 * @param string $serveur
 * @return array
 */
function polyhier_get_parents($id_objet,$objet,$serveur=''){

	$in = (is_array($id_objet) ? sql_in('id_objet',$id_objet) : ("id_objet=".intval($id_objet)));
	$where = "($in) AND objet=".sql_quote($objet);

	// selectionner l'intersection entre base et tableau
	$id_parents = sql_allfetsel("id_parent","spip_rubriques_liens",$where,"","","","",$serveur);
	$id_parents = array_map('reset',$id_parents);

	return $id_parents;
}

/**
 *
 * @param int/array $id_parent
 * @param string $objet Un éventuel type d'objet
 * @param string $serveur
 * @return array Retourne un tableau des enfants triés par objet : array('article' => array(1, 2, 3))
 */
function polyhier_get_enfants($id_parent, $objet='', $serveur=''){

	$where = (is_array($id_parent) ? sql_in('id_parent',$id_parent) : ("id_parent=".intval($id_parent)));
	if ($objet){
		$where_objet = is_array($objet) ? sql_in('objet', $objet) : 'objet = '.sql_quote($objet);
		$where .= ' and '.$where_objet;
	}

	// selectionner l'intersection entre base et tableau
	$objets = sql_allfetsel("objet, id_objet","spip_rubriques_liens",$where,"","","","",$serveur);
	
	if (!$objets or !is_array($objets)){
		return array();
	}
	else{
		$objets_tries = array();
		foreach ($objets as $couple){
			if (isset($objets_tries[$couple['objet']])){
				$objets_tries[$couple['objet']][] = $couple['id_objet'];
			}
			else{
				$objets_tries[$couple['objet']] = array($couple['id_objet']);
			}
		}
		return $objets_tries;
	}
}

/**
 * Retrouver tous les parents, directs et indirects
 * 
 * @param int|array $id_objet
 * @param string $objet
 * @param string $serveur
 * @return array
 */
function polyhier_get_allparents($id_objet,$objet,$serveur=''){
	$table_sql = table_objet_sql($objet);
	$primary = id_table_objet($objet);

	$in = (is_array($id_objet) ? sql_in($primary,$id_objet) : ("$primary=".intval($id_objet)));
	$directs = sql_allfetsel($objet=='rubrique'?'id_parent':'id_rubrique', $table_sql, $in,'','','','',$serveur);
	$directs = array_map('reset',$directs);

	$indirects = polyhier_get_parents($id_objet, $objet, $serveur);

	$id_parents = array_merge($directs,$indirects);
	return $id_parents;
}


/**
 * Publier une rubrique et ses parents directs/indirects
 *
 * @param int|array $id_rubrique
 * @param string $date
 * @return bool
 */
function polyhier_publier_branche_rubrique($id_rubrique, $date=null){
	if (is_null($date))
		$date = date('Y-m-d H:i:s');
	$changed = false;

	$maxiter = 100;
	if (!is_array($id_rubrique))
		$id_rubrique = array($id_rubrique);

	while (count($id_rubrique) AND $maxiter--) {
		sql_updateq('spip_rubriques', array('statut'=>'publie', 'date'=>$date), sql_in('id_rubrique',$id_rubrique));
		#spip_log("publier rubriques ".var_export($id_rubrique,true),'polyhier');
	
		$id_rubrique = polyhier_get_allparents($id_rubrique,'rubrique');
		if (count($id_rubrique)){
			// ne garder que celles qui ne sont pas deja publiees
			$id_rubrique = sql_allfetsel(
							'id_rubrique',
							'spip_rubriques',
							"(statut<>'publie' OR date<".sql_quote($date).') AND '.sql_in('id_rubrique',$id_rubrique));
			$id_rubrique = array_map('reset',$id_rubrique);
		}
		if (count($id_rubrique))
			$changed = true;
	}

	return $changed;
}

/**
 * Fonction a appeler lorsqu'on depublie ou supprime qqch dans une rubrique
 * retourne Vrai si le statut change effectivement
 *
 * http://doc.spip.org/@depublier_branche_rubrique_if
 *
 * @param int|array $id_rubrique
 * @return bool
 */
function polyhier_depublier_branche_rubrique_if($id_rubrique){
	$date = date('Y-m-d H:i:s'); // figer la date
	$changed = false;
	#spip_log("polyhier_depublier_branche_rubrique_if ".var_export($id_rubrique,true),"polyhier");

	$maxiter = 100;
	if (!is_array($id_rubrique))
		$id_rubrique = array($id_rubrique);

	while (count($id_rubrique) AND $maxiter--) {
		$ids = array();
		foreach($id_rubrique as $id)
			if (depublier_rubrique_if($id,$date)) {
				$ids[] = $id;
				#spip_log("depublier rubrique $id",'polyhier');
			}

		if (!count($ids))
			return $changed;
		$changed = true;

		// recuperer toutes les parentes directes et indirectes
		// des rubriques effectivement depubliees
		$id_rubrique = polyhier_get_allparents($ids,'rubrique');
		// ne garder que celles qui sont publiees
		if (count($id_rubrique)){
			$id_rubrique = sql_allfetsel(
							'id_rubrique',
							'spip_rubriques',
							"statut='publie' AND ".sql_in('id_rubrique',$id_rubrique));
			$id_rubrique = array_map('reset',$id_rubrique);
		}
	}

	return $changed;
}



/**
 * Fonction a appeler lorsque le statut d'un objet change dans une rubrique
 * ou que la rubrique est deplacee.
 * Le 2e arg est un tableau ayant un index "statut" (indiquant le nouveau)
 * et "remove" indiquant les rubriques quittes en parent
 * et "add" indiquant les rubriques ajoutees en parent
 *
 * Si le statut passe a "publie", la rubrique et ses parents y passent aussi
 * et les langues utilisees sont recalculees.
 *
 * Consequences symetriques s'il est depublie'.
 * S'il est deplace' alors qu'il etait publiee, double consequence.
 *
 * @param array $id_parents
 * @param array $modifs
 * @param string $statut_ancien
 * @param bool|string $postdate
 */
function polyhier_calculer_rubriques_if ($id_parents, $modifs, $statut_ancien='', $postdate = false){
	$neuf = false;
	$time = time();

	if ($statut_ancien == 'publie') {
		if (isset($modifs['statut'])
			OR ($postdate AND strtotime($postdate)>$time))
			$neuf |= polyhier_depublier_branche_rubrique_if($id_parents);
		if (count($modifs['remove']))
			$neuf |= polyhier_depublier_branche_rubrique_if($modifs['remove']);
		// ne publier que si c'est pas un postdate, ou si la date n'est pas dans le futur
		if ($postdate){
			calculer_prochain_postdate(true);
			$neuf |= (strtotime($postdate)<=$time); // par securite
		}
		elseif(count($modifs['add']))
			$neuf |= polyhier_publier_branche_rubrique($modifs['add']);
	}
	elseif ($modifs['statut']=='publie'){
		if ($postdate){
			calculer_prochain_postdate(true);
			$neuf |= (strtotime($postdate)<=$time); // par securite
		}
		else
			$neuf |= polyhier_publier_branche_rubrique($id_parents);
	}

	if ($neuf)
		// Sauver la date de la derniere mise a jour (pour menu_rubriques)
	  ecrire_meta("date_calcul_rubriques", date("U"));

	$langues = calculer_langues_utilisees();
	ecrire_meta('langues_utilisees', $langues);
}
?>
