<?php

function traduction_hydraulic($param_a_traiter)
{
   return _T('hydraulic:'.$param_a_traiter);
}

function decoupeIdSection($param_a_decoup){
	
	$decoup = explode('_', $param_a_decoup, 3);
	return _T('hydraulic:section_'.$decoup[count($decoup)-1]);
}
?>
