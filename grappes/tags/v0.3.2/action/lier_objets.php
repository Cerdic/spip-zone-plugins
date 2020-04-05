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
		minipres(_T('grappes:action_inconnue',array('action'=>$action)));
	}

	if (!autoriser('associer',$source,$id_source)){
		include_spip('inc/minipres');
		minipres(_T('grappes:autoriser_associer_non'));
	}

	if ($action == 'lier')
		lier_objets($source,$id_source,$cible,$id_cible);
	elseif ($action == 'delier')
		delier_objets($source,$id_source,$cible,$id_cible);

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_grappe/$id_source'");
}


function lier_objets($source,$id_source,$cible,$id_cible){
	list($table,$type,$ids,$idc,$rang) = trouver_table_liaison($source,$cible);
	if ($table) {
		$nb = sql_countsel($table,"$ids=".sql_quote($id_source))+1;
		if ($type=='id') {
			if (!sql_countsel($table,array("'$ids'=".sql_quote($id_source), "'$idc'=".sql_quote($id_cible)))){
				if ($rang){
					sql_insertq($table,array($ids=>$id_source,$idc=>$id_cible,'rang'=>$nb));
				}else{
					sql_insertq($table,array($ids=>$id_source,$idc=>$id_cible));
				}
			}
		}
		elseif ($type=='lien_source') {
			$cible = objet_type($cible);
			if (!sql_countsel($table, array("'$ids'=" . sql_quote($id_source), "'objet'=" . sql_quote($cible), "'id_objet'=" . sql_quote($id_cible)))){
				if ($rang){
					sql_insertq($table,array($ids=>$id_source,'objet'=>$cible,'id_objet'=>$id_cible,'rang'=>$nb));
				}else{
					sql_insertq($table,array($ids=>$id_source,'objet'=>$cible,'id_objet'=>$id_cible));
				}
			}
		}
		elseif ($type=='lien_cible') {
			$source = objet_type($source);
			if (!sql_countsel($table, array("'$idc'=" . sql_quote($id_cible), "'objet'=" . sql_quote($source), "'id_objet'=" . sql_quote($id_source)))){
				if($rang){
					sql_insertq($table,array($idc=>$id_cible,'objet'=>$source,'id_objet'=>$id_source,'rang'=>$nb));
				}else{
					sql_insertq($table,array($idc=>$id_cible,'objet'=>$source,'id_objet'=>$id_source));
				}
			}
		}
	}
}

function delier_objets($source,$id_source,$cible,$id_cible){
	list($table,$type,$ids,$idc) = trouver_table_liaison($source,$cible);
	if ($table) {
		if ($type=='id') {
			sql_delete($table,array("$ids=".sql_quote($id_source), "$idc=".sql_quote($id_cible)));
		}
		elseif ($type=='lien_source') {
			$cible = objet_type($cible);
			sql_delete($table, array("$ids=" . sql_quote($id_source), "objet=" . sql_quote($cible), "id_objet=" . sql_quote($id_cible)));
		}
		elseif ($type=='lien_cible') {
			$source = objet_type($source);
			sql_delete($table, array("$idc=" . sql_quote($id_cible), "objet=" . sql_quote($source), "id_objet=" . sql_quote($id_source)));
		}
	}
}

//
// retourne array(nom_table_sql, type_liaison, ids, idc)
// ou ids et idc sont les noms des cles primaires des objets
function trouver_table_liaison($source,$cible){
	global $table_des_tables;
	$source = objet_type($source);
	$cible = objet_type($cible);
	$ts = table_objet($source);
	$tc = table_objet($cible);
	$ids = id_table_objet($source);
	$idc = id_table_objet($cible);

	$trouver_table = charger_fonction('trouver_table', 'base');

	// chercher d'abord les tables : spip_sources_cibles (id_source, id_cible)
	if (($d = $trouver_table($ts.'_'.$tc))
	or ($d = $trouver_table($tc.'_'.$ts))){
		$rang = isset($d['field']['rang']);
		return array($d['table'],'id', $ids, $idc,$rang);
	}
	// sinon chercher spip_sources_liens (id_source, cible, id_cible)
	if ($d = $trouver_table($ts.'_liens')){
		$rang = isset($d['field']['rang']);
		return array($d['table'],'lien_source', $ids, $idc,$rang);}
	// sinon chercher spip_cibles_liens (id_source, cible, id_cible)
	if ($d = $trouver_table($tc.'_liens')){
		$rang = isset($d['field']['rang']);
		return array($d['table'],'lien_cible', $ids, $idc,$rang);
	}
}
?>
