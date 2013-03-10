<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function timelineblog_insert_head_css($flux){
	$flux.= '<link rel="stylesheet" type="text/css" href="'.find_in_path('timelineblog.css').'" />';
	return $flux;
}

?>
