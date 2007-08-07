<?php
/*
 * Recherche entendue
 * plug-in d'outils pour la recherche et l'indexation
 * Panneaux de controle admin_index et index_tous
 * Boucle INDEX
 * filtre google_like
 *
 *
 * Auteur :
 * cedric.morin@yterium.com
 * pdepaepe et Nicolas Steinmetz pour google_like
 * fil pour le panneau admin_index d'origine
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

include_spip('base/indexation_etendue');

if (!isset($GLOBALS['meta']['Recherche_etendue_exceptions'])){
	include_spip('inc/indexation_etendue');
	update_index_tables_sql_from_meta();
}
if (isset($GLOBALS['meta']['Recherche_etendue_exceptions'])){
	global $exceptions_des_tables;
	$table_objet_vers_id = unserialize($GLOBALS['meta']['Recherche_etendue_exceptions']);
	foreach($table_objet_vers_id as $key=>$value)
		$exceptions_des_tables['index'][$key] = $value;
}


// {recherche}
// http://www.spip.net/@recherche
// gestion du cas ou le critere recherche est applique a la boucle INDEX
// sion renvoi vers la boucle _dist
function critere_recherche($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	if ($boucle->id_table=='index'){
		// Ne pas executer la requete en cas de hash vide
		$boucle->hash = '
	// RECHERCHE
	list($rech_select, $rech_where) = RechercheEtendue_prepare_index_recherche($GLOBALS["recherche"], "'.$crit->cond.'");
		';
		// Sauf si le critere est conditionnel {recherche ?}
		if (!$crit->cond)
			$boucle->hash .= '
	if ($rech_where) ';

		foreach($boucle->select as $key=>$sel){
			if ($sel == $boucle->id_table . '.points')
				$boucle->select[$key]='1';
	 	}
		$boucle->select[] = '$rech_select AS points';
		// et la recherche trouve
		$boucle->where[] = '$rech_where';
	}
	else
		critere_recherche_dist($idb, $boucles, $crit);
}

//
// <BOUCLE(INDEX)>
//
function boucle_INDEX_dist($id_boucle, &$boucles) {
	global $table_des_tables;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_index";

	// rajouter l'id de la boucle devant le id_table contenu dans les champs calcules
	foreach($boucle->select as $key=>$select)
		$boucle->select[$key]=preg_replace('{\(id_table=}','('.$boucle->id_table.'.id_table=',$select);
	//$boucle->select[] = $boucle->id_table.'.id_table as id_table';

	$boucle->group[] = 'CONCAT(' . $boucle->id_table . '.id_table,\':\','.$boucle->id_table . '.id_objet)';
	
	/*static $table_abr=array();
	static $table_full=array();
	static $liste_tables=array();
	if (!count($table_abr)){
		foreach($table_des_tables as $one=>$repl){
			if ($one!='index'){
				$table_abr[] = "$one";
				$table_full[] = "spip_$repl";
			}
		}
		$liste_tables = liste_index_tables();
	}*/
	
	/*foreach($boucle->where as $key=>$where){
		if (!is_array($where)){
			if (strpos($where,'id_table')!==FALSE){
				// on regarde si y a des criteres nom_table=articles
				// pour remplacer par nom_table=spip_articles
				$where = str_replace($table_abr, $table_full, $where);
				$where = str_replace(array_values($liste_tables),array_keys($liste_tables),$where);
				$boucle->where[$key] = $where;
			}
		}
		else {
			if (strpos($where[1],'id_table')!==FALSE){
				for ($k=1;$k<count($where);$k++){
					$where[$k] = str_replace($table_abr, $table_full, $where[$k]);
					$where[$k] = str_replace(array_values($liste_tables),array_keys($liste_tables),$where[$k]);
				}
				$boucle->where[$key] = $where;
			}
		}
	}*/
	
	foreach($boucle->order as $key=>$order){
		$boucle->order[$key]=str_replace($boucle->id_table . '.points','points',$order);
	}
	return calculer_boucle($id_boucle, $boucles);
}

?>