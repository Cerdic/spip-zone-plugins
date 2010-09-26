<?php

function notation_header_prive($flux){
	$flux = notation_insert_head($flux);
	return $flux;
}

function notation_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/notation.v2.css').'" type="text/css" media="all" />';
	return $flux;
}

function notation_affichage_final($flux){
    if (strpos($flux, "'notation_note notation_note_on_load'") === false)
		return $flux;
	$incHead = "";
	$incHead .= "<script src='".find_in_path('javascript/jquery.MetaData.js')." type='text/javascript'></script>\n";
	$incHead .= "<script src='".find_in_path('javascript/jquery.rating.js')." type='text/javascript'></script>\n";
	$incHead .= "<script src='".find_in_path('javascript/notation.js')." type='text/javascript'></script>\n";
	include_spip('inc/filtres');
	if(function_exists('compacte_head')){
		$incHead = compacte_head($incHead);
	}
	return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);

}

?>