<?php
/**
 * Plugin QuickFlip pour Spip 3
 * Licence GPL (c)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function quickflip_insert_head_css($flux){
    $flux .= '<link rel="stylesheet" href="'.find_in_path('css/quickflip.css').'" type="text/css" media="all" />';
	return $flux;
}

function quickflip_insert_head($flux){
	$flux .= '
<script src="'.find_in_path('js/jquery.quickflip.min.js').'" type="text/javascript"></script>
<script src="'.find_in_path('js/quickflip.js').'" type="text/javascript"></script>
';
	return $flux;
}

function quickflip_header_prive($flux){
	$flux .= '
<link rel="stylesheet" href="'.find_in_path('css/quickflip.css').'" type="text/css" media="all" />
<script src="'.find_in_path('js/jquery.quickflip.min.js').'" type="text/javascript"></script>
<script src="'.find_in_path('js/quickflip.js').'" type="text/javascript"></script>
';
	return $flux;
}
?>