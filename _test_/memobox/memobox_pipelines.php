<?php

function memobox_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('memobox.css').'" type="text/css" media="projection, screen" />';
	$flux .= "<script type='text/javascript' src='".generer_url_public('memobox','lang='.$GLOBALS["spip_lang"])."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path('memobox_interface.js')."'></script>\n";
	return $flux;
}
?>
