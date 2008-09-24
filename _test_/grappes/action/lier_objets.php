<?php
/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

function action_lier_objets_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($action,$source,$id_source,$cible,$id_cible) = explode('/',$arg);
	
	if ($action != 'lier' AND $action != 'delier') {
		include_spip('inc/minipres');
		minipres(_L('Action '.$action.' inconnue'));
	}
	
	if (!autoriser('associer',$source,$id_source)){
		include_spip('inc/minipres');
		minipres(_L('Vous n\'&ecirc;tes pas autoris&eacute; &agrave; effectuer cette action !'));		
	}
	
	if ($action == 'lier')
		lier_objets($source,$id_source,$cible,$id_cible);
	elseif ($action == 'delier')
		delier_objets($source,$id_source,$cible,$id_cible);
}


function lier_objets($source,$id_source,$cible,$id_cible){
	list($table,$type) = trouver_table_liaison($source,$cible);
	if ($table) {
		$ids = id_table_objet($source);
		if ($type=='id') {
			$idc = id_table_objet($cible);
			if (!sql_countsel($table,array("'$ids'=".sql_quote($id_source), "'$idc'=".sql_quote($id_cible)))){
				sql_insertq($table,array($ids=>$id_source,$idc=>$id_cible));
			}
		}
		elseif ($type=='lien') {
			if (!sql_countsel($table, array("'$ids'=" . sql_quote($id_source), "'objet'=" . sql_quote($cible), "'id_objet'=" . sql_quote($id_cible)))){
				sql_insertq($table,array($ids=>$id_source,'objet'=>$cible,'id_objet'=>$id_cible));
			}		
		}
	}
}

function delier_objets($source,$id_source,$cible,$id_cible){
	list($table,$type) = trouver_table_liaison($source,$cible);
	if ($table) {
		$ids = id_table_objet($source);
		if ($type=='id') {
			$idc = id_table_objet($cible);
			sql_delete($table,array("$ids=".sql_quote($id_source), "$idc=".sql_quote($id_cible)));
		}
		elseif ($type=='lien') {
			sql_delete($table, array("$ids=" . sql_quote($id_source), "objet=" . sql_quote($cible), "id_objet=" . sql_quote($id_cible)));
		}
	}	
}

//
// retourne array(nom_table_sql, type_liaison)
function trouver_table_liaison($source,$cible){
	global $table_des_tables;
	$source = objet_type($source);
	$cible = objet_type($cible);
	$ts = table_objet($source);
	$tc = table_objet($cible);
	
	$trouver_table = charger_fonction('trouver_table', 'base');
	
	// chercher d'abord les tables : spip_sources_cibles (id_source, id_cible)
	if (($d = $trouver_table($ts.'_'.$tc))
	or ($d = $trouver_table($tc.'_'.$ts)))
		return array($d['table'],'id');
	// sinon chercher spip_sources_liens (id_source, cible, id_cible)
	if ($d = $trouver_table($ts.'_liens')){
		return array($d['table'],'lien');}

}
?>
