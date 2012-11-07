<?php

function anaclic_insert_head($flux)
{
	$flux .= 
'<link rel="stylesheet" href="'._DIR_PLUGIN_ANACLIC.'anaclic.css" type="text/css"  />
';
	return $flux;
}

?>