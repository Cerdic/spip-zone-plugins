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
	return $flux;
}

function spiip_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('style_spiip_public',$args).'" type="text/css" media="projection, screen" />';
	$flux .= "<script type='text/javascript' src='".find_in_path('dist_back/pagination-ahah.js')."'></script>\n";
	if (_request('jqdb')!==NULL)
		$flux = '<script src="'.find_in_path('jquery_uncompressed.js').'" type="text/javascript"></script>'.$flux;
	else
		$flux = '<script src="'.find_in_path('jquery.lite.213.js').'" type="text/javascript"></script>'.$flux;
	return $flux;
}


?>
