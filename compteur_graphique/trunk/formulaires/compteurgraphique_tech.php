<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_compteurgraphique_tech_charger_dist(){	
	$valeurs=array();
	return $valeurs;
}

function formulaires_compteurgraphique_tech_traiter_dist(){
	$CG_nom_table = "spip_compteurgraphique";
	$res = array('editable'=>true);
	$res['message_ok'] = 'Aucune modification n\'a &eacute;t&eacute; enregistr&eacute;e';
	
	if (_request('config_CG')=='oui') {
		$res['message_ok'] = 'Configuration enregistrée';
        sql_delete($CG_nom_table,"statut = 9");
	}
	if (_request('config_CG')=='non') {
		$res['message_ok'] = 'Configuration enregistrée';
		sql_delete($CG_nom_table,"statut = 9");
		sql_insertq($CG_nom_table,array("statut" => 9));
	}
	if (_request('genere_CG_miniatures')=='gif') {
		sql_delete($CG_nom_table,"statut = 11");
	}
	if (_request('genere_CG_miniatures')=='png') {
		sql_delete($CG_nom_table,"statut = 11");
		sql_insertq($CG_nom_table,array("statut" => 11));
	}
	if (_request('genere_CG')=='gif') {
		sql_delete($CG_nom_table,"statut = 12");
	}
	if (_request('genere_CG')=='png') {
		sql_delete($CG_nom_table,"statut = 12");
		sql_insertq($CG_nom_table,array("statut" => 12));
	}
	if (_request('transparent_CG')=='oui') {
		sql_delete($CG_nom_table,"statut = 13");
	}
	if (_request('transparent_CG')=='non') {
		sql_delete($CG_nom_table,"statut = 13");
		sql_insertq($CG_nom_table,array("statut" => 13));
	}
	return $res;
}

