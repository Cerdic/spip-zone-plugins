<?php

if (!isset($GLOBALS['diapo_temps']))
	$GLOBALS['diapo_temps']=8000;
if (!isset($GLOBALS['diapo_grand']))
	$GLOBALS['diapo_grand']=560;
if (!isset($GLOBALS['diapo_vignettes']))
	$GLOBALS['diapo_vignettes']=8;
if (!isset($GLOBALS['diapo_vignette']))
	$GLOBALS['diapo_vignette']=floor($GLOBALS['diapo_grand']/$GLOBALS['diapo_vignettes']-2);
if (!isset($GLOBALS['diapo_petit']))
	$GLOBALS['diapo_petit']=floor($GLOBALS['diapo_grand']-($GLOBALS['diapo_grand']/$GLOBALS['diapo_vignettes']*2)-10);

function balise_DIAPO_TEMPS_dist($p) {
	$p->code = "\$GLOBALS['diapo_temps']";
	return $p;
}
function balise_DIAPO_GRAND_dist($p) {
	$p->code = "\$GLOBALS['diapo_grand']";
	return $p;
}
function balise_DIAPO_PETIT_dist($p) {
	$p->code = "\$GLOBALS['diapo_petit']";
	return $p;
}
function balise_DIAPO_VIGNETTE_dist($p) {
	$p->code = "\$GLOBALS['diapo_vignette']";
	return $p;
}
function balise_DIAPO_VIGNETTES_dist($p) {
	$p->code = "\$GLOBALS['diapo_vignettes']";
	return $p;
}


?>