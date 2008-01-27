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
// {fusion_patronyme} permet de classer par importance les patronymes
//
function critere_fusion_patronyme($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	$type = $boucle->type_requete;
	$patronyme = $boucle->id_table.'.patronyme';
	$boucles[$idb]->group[] = $patronyme;
	$boucles[$idb]->select[] = $patronyme . ',  COUNT(' . $patronyme . ')';
}

?>