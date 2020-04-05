<?php

// Renvoit la traduction du paramètre à traiter
function traduction_hydraulic($param_a_traiter) {
	return _T('hydraulic:'.$param_a_traiter);
}

// Découpe l'id de la section passé en paramètre et retourne la traduction adéquat
function decoupeIdSection($param_a_decoup) {
	$decoup = explode('_', $param_a_decoup, 3);
	return _T('hydraulic:section_'.$decoup[count($decoup)-1]);
}
?>