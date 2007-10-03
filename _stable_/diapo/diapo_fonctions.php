<?php

if (!isset($GLOBALS['diapo_temps']))
	$GLOBALS['diapo_temps']=8000;
if (!isset($GLOBALS['diapo_grand']))
	$GLOBALS['diapo_grand']=560;
if (!isset($GLOBALS['diapo_grand_h']))
//	$GLOBALS['diapo_grand_h']=0;
	$GLOBALS['diapo_grand_h']=$GLOBALS['diapo_grand'];
if (!isset($GLOBALS['diapo_vignettes']))
	$GLOBALS['diapo_vignettes']=8;
if (!isset($GLOBALS['diapo_vignette']))
	$GLOBALS['diapo_vignette']=floor($GLOBALS['diapo_grand']/$GLOBALS['diapo_vignettes']-2);
if (!isset($GLOBALS['diapo_vignette_h']))
//	$GLOBALS['diapo_vignette_h']=0;
	$GLOBALS['diapo_vignette_h']=$GLOBALS['diapo_vignette'];
if (!isset($GLOBALS['diapo_petit']))
	$GLOBALS['diapo_petit']=floor($GLOBALS['diapo_grand']-($GLOBALS['diapo_grand']/$GLOBALS['diapo_vignettes']*2)-10);
if (!isset($GLOBALS['diapo_petit_h']))
//	$GLOBALS['diapo_petit_h']=0;
	$GLOBALS['diapo_petit_h']=$GLOBALS['diapo_petit'];

function balise_DIAPO_TEMPS_dist($p) {
	$p->code = "\$GLOBALS['diapo_temps']";
	return $p;
}
function balise_DIAPO_GRAND_dist($p) {
	$p->code = "\$GLOBALS['diapo_grand']";
	return $p;
}
function balise_DIAPO_GRAND_H_dist($p) {
	$p->code = "\$GLOBALS['diapo_grand_h']";
	return $p;
}
function balise_DIAPO_PETIT_dist($p) {
	$p->code = "\$GLOBALS['diapo_petit']";
	return $p;
}
function balise_DIAPO_PETIT_H_dist($p) {
	$p->code = "\$GLOBALS['diapo_petit_h']";
	return $p;
}
function balise_DIAPO_VIGNETTE_dist($p) {
	$p->code = "\$GLOBALS['diapo_vignette']";
	return $p;
}
function balise_DIAPO_VIGNETTE_H_dist($p) {
	$p->code = "\$GLOBALS['diapo_vignette_h']";
	return $p;
}
function balise_DIAPO_VIGNETTES_dist($p) {
	$p->code = "\$GLOBALS['diapo_vignettes']";
	return $p;
}


?>