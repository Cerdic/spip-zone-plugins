<?php

function agrandir($coords, $fact){
	$Tcoords = explode(',',$coords);
	$Tret = array();
	foreach ($Tcoords as $c) $Tret[] = ($c * $fact); 
	return join(',', $Tret);
}

?>
