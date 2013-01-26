<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * insertion du css
 **/
function zforumart_insert_head_css($flux){
	$css      = find_in_path('zforumart.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
    return $flux;

}


?>
