<?php
/* *********************************************************************
   *
   * Copyright (c) 2006-2008
   * Xavier Burot
   * fichier : public/genea_boucles.php
   *
   * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
   *
   *********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/genea_base');

// -- Definition des boucles utilisables --------------------------------

//
// <BOUCLE(GENEA)>
//
function boucle_GENEA_dist($id_boucle, &$boucles) {
	global $table_prefix;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = $table_prefix.'_genea';
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(GENEA_INDIVIDUS)>
//
function boucle_GENEA_INDIVIDUS_dist($id_boucle, &$boucles) {
	global $table_prefix;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = $table_prefix.'_genea_individus';
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(GENEA_EVT)>
//
function boucle_GENEA_EVT_dist($id_boucle, &$boucles) {
	global $table_prefix;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = $table_prefix.'_genea_evt';
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(GENEA_SOURCES)>
//
function boucle_GENEA_SOURCES_dist($id_boucle, &$boucles) {
	global $table_prefix;
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = $table_prefix.'_genea_sources';
	return calculer_boucle($id_boucle, $boucles);
}

// -- Definition de criteres supplementaires ----------------------------

//
// {importance xxx} permet de classer par importance un champ de la table
//
function critere_importance($idb, &$boucles, $crit){
	global $table_prefix;
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$champ = $boucle->id_table . '.' . $type;
	$boucle->select[] = 'COUNT('.$champ.') AS importance';
	$boucles[$idb]->group[] = $champ;
}
//
// -- Balise qui permet de lire le champ IMPORTANCE créé par {importance xxx}
//
function balise_IMPORTANCE_dist($p){
	$p->code = '$Pile[$SP][\'importance\']';
	$p->interdire_scripts = false;
	return $p;
}

?>