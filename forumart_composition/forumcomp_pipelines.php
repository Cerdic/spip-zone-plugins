<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * insertion du css
 **/
function forumcomp_insert_head_css($flux){
	$css      = find_in_path('forumcomp.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
    return $flux;

}


?>
