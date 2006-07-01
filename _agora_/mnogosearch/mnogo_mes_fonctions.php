<?php

function balise_MNOGO_RECHERCHE_dist($p) {
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats_synthese']['MNOGO_RECHERCHE']:''";
	return $p;
}
function balise_MNOGO_RESUME_RESULTATS_dist($p) {
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats_synthese']['MNOGO_RESUME_RESULTATS']:''";
	return $p;
}
function balise_MNOGO_PREMIER_dist($p) {
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats_synthese']['MNOGO_PREMIER']:''";
	return $p;
}
function balise_MNOGO_DERNIER_dist($p) {
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats_synthese']['MNOGO_DERNIER']:''";
	return $p;
}
function balise_MNOGO_TOTAL_dist($p) {
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL']:''";
	return $p;
}
function balise_MNOGO_SEARCHTIME_dist($p) {
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats_synthese']['MNOGO_SEARCHTIME']:''";
	return $p;
}


function balise_MNOGO_ITEM_NUMERO_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_NUMERO']:''";
	return $p;
}
function balise_MNOGO_ITEM_TITRE_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_TITRE']:''";
	return $p;
}
function balise_MNOGO_ITEM_URL_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_URL']:''";
	return $p;
}
function balise_MNOGO_ITEM_POINTS_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_POINTS']:''";
	return $p;
}
function balise_MNOGO_ITEM_POPULARITE_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_POPULARITE']:''";
	return $p;
}
function balise_MNOGO_ITEM_DESCRIPTIF_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_DESCRIPTIF']:''";
	return $p;
}
function balise_MNOGO_ITEM_TAILLE_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_TAILLE']:''";
	return $p;
}
function balise_MNOGO_ITEM_TYPE_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_TYPE']:''";
	return $p;
}
function balise_MNOGO_ITEM_DATE_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_DATE']:''";
	return $p;
}
function balise_MNOGO_ITEM_CACHE_URL_dist($p) {
	$_arg='0';
	if ($p->param && !$p->param[0][0]){
		$_arg =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	$p->code = "mnogo_checkresults()?\$GLOBALS['mnogo_resultats'][$_arg]['MNOGO_ITEM_CACHE_URL']:''";
	return $p;
}
?>