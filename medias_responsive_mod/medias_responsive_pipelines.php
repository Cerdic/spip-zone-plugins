<?php



function medias_responsive_mod_insert_head_css($flux) {
	$flux = "\n<link rel='stylesheet' type='text/css' media='all' href='".direction_css(find_in_path("css/medias_responsive.css"))."'>\n".$flux;


	return $flux;
}

function medias_responsive_mod_insert_head($flux) {
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/rAF.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/portfolio_ligne.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/medias_responsive.js")."'></script>\n";
	return $flux;
}

function medias_responsive_mod_header_prive($flux) {
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/rAF.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/portfolio_ligne.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/medias_responsive.js")."'></script>\n";
	$flux .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".find_in_path("css/medias_responsive.css")."\">\n";
	return $flux;
}

function medias_responsive_mod_post_echappe_html_propre($txt) {
	$txt = preg_replace (",</ul>[\r\n\ ]*<ul class=\"portfolio_ligne\">,", "", $txt);
	return $txt;
}