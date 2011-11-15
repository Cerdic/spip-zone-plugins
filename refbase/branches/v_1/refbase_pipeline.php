<?php
function refbase_insert_head($flux){
	$flux .= "<script type='text/javascript' src='spip.php?page=refbase_js'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('refbase.css').'" type="text/css" />';
	return $flux;
}

function refbase_header_prive($flux){
	$flux .= "<script type='text/javascript' src='../spip.php?page=refbase_js'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('refbase.css').'" type="text/css" />';
	return $flux;
}
?>
