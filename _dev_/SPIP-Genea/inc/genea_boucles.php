<?php
/*	*********************************************************************
	*
	* Copyright (c) 2006
	* Xavier Burot
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/genea_base');

// -- Definition des boucles utilisables --------------------------------

//
// <BOUCLE(INDIVIDUS)>
//
function boucle_INDIVIDUS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = "spip_genea_individus";
	return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(GENEA_EVT)>
//
function boucle_GENEA_EVT_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] = "spip_genea_evt";
	return calculer_boucle($id_boucle, $boucles);
}?>