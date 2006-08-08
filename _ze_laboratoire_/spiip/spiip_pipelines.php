<?php

function spiip_header_prive($flux){
	global $spip_lang, $couleur_claire, $couleur_foncee;
	$args = "couleur_claire=" .
		substr($couleur_claire,1) .
		'&couleur_foncee=' .
		substr($couleur_foncee,1) .
		'&ltr=' . 
		$GLOBALS['spip_lang_left'];
		
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('style_spiip_prive',$args).'" type="text/css" media="projection, screen" />';
	if (_request('jqdb')!==NULL)
		$flux .= '<script src="'.find_in_path('jquery_uncompressed.js').'" type="text/javascript"></script>';
	else
		$flux .= '<script src="'.find_in_path('jquery.js').'" type="text/javascript"></script>';
	$flux .= "<script type='text/javascript' src='".find_in_path('dist_back/gadget-rubriques.js')."'></script>\n";
	$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('dist_back/gadget-rubriques.css')).'" type="text/css" media="projection, screen" />';
	return $flux;
}
?>