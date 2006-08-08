<?php

function spiip_header_prive($flux){
	if (_request('jqdb')!==NULL)
		$flux .= '<script src="'.find_in_path('jquery_uncompressed.js').'" type="text/javascript"></script>';
	else
		$flux .= '<script src="'.find_in_path('jquery.js').'" type="text/javascript"></script>';
	$flux .= "<script type='text/javascript' src='".find_in_path('dist_back/gadget-rubriques.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.find_in_path('dist_back/gadget-rubriques.css').'" type="text/css" media="projection, screen" />';
	return $flux;
}
?>