<?php

function notation_header_prive($flux){
	$flux = notation_insert_head($flux);
	return $flux;
}

function notation_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_NOTATION.'css/notation.v2.css" type="text/css" media="all" />';
	return $flux;
}

function notation_affichage_final($flux){
    if (strpos($flux, "'notation_note notation_note_on_load'") === false)
		return $flux;
	$incHead = "";
	$incHead .= "<script src='"._DIR_PLUGIN_NOTATION."javascript/jquery.MetaData.js' type='text/javascript'></script>\n";
	$incHead .= "<script src='"._DIR_PLUGIN_NOTATION."javascript/jquery.rating.js' type='text/javascript'></script>\n";
	$incHead .= "<script src='"._DIR_PLUGIN_NOTATION."javascript/notation.js' type='text/javascript'></script>\n";
	include_spip('inc_filtres');
	$incHead = compacte_head($incHead);
	return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);

}

?>