<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');

function jquerysuperfish_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$conf_jquerysuperfish = lire_config('jquerysuperfish');
		$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('css/superfish.css')).'" type="text/css" media="all" />';
		if($conf_jquerysuperfish["menu_vert"]) {
			$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('css/superfish-vertical.css')).'" type="text/css" media="all" />';
		}
		if($conf_jquerysuperfish["menu_navbar"]) {
			$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('css/superfish-navbar.css')).'" type="text/css" media="all" />';
		}
	}

	return $flux;
}

function jquerysuperfish_insert_head($flux){
	$conf_jquerysuperfish = lire_config('jquerysuperfish');
	$str_supersubs = "";
	if($conf_jquerysuperfish["supersubs"]) {
		$flux .= "\n".'<script src="'.url_absolue(find_in_path('javascript/supersubs.js')).'" type="text/javascript"></script>';
	}
	$flux .= "\n".'<script src="'.url_absolue(find_in_path('javascript/hoverIntent.js')).'" type="text/javascript"></script>';
	$flux .= "\n".'<script src="'.url_absolue(find_in_path('javascript/superfish.js')).'" type="text/javascript"></script>';
	$flux .= "\n".'<script type="text/javascript">/* <![CDATA[ */'."\n".'jQuery(document).ready(function(){' ;

	if($conf_jquerysuperfish["supersubs"]) {
		$str_supersubs = ".supersubs({
		minWidth:".$conf_jquerysuperfish["supersubs_minwidth"].",
		maxWidth:".$conf_jquerysuperfish["supersubs_maxwidth"].",
		extraWidth:".$conf_jquerysuperfish["supersubs_extrawidth"]."
		})";
	}
	if($conf_jquerysuperfish["menu_hori"]) {
		$flux .= "\n".'var params_h = {};' ;
		if($conf_jquerysuperfish["animation_hori"]) $flux .= "\n".'params_h.animation = {'.$conf_jquerysuperfish["animation_hori"].'};' ;
		if($conf_jquerysuperfish["delai_hori"]) $flux .= "\n".'params_h.delay = '.$conf_jquerysuperfish["delai_hori"].';' ;
		$flux .= "\n".'$(".'.$conf_jquerysuperfish["classe_hori"].'").addClass("sf-menu sf-shadow")'.$str_supersubs.'.superfish(params_h);';
	}
	if($conf_jquerysuperfish["menu_vert"]) {
		$flux .= "\n".'var params_v = {};' ;
		if($conf_jquerysuperfish["animation_vert"]) $flux .= "\n".'params_v.animation = {'.$conf_jquerysuperfish["animation_vert"].'};' ;
		if($conf_jquerysuperfish["delai_vert"]) $flux .= "\n".'params_v.delay = '.$conf_jquerysuperfish["delai_vert"].';' ;
		$flux .= "\n".'$(".'.$conf_jquerysuperfish["classe_vert"].'").addClass("sf-menu sf-vertical sf-shadow")'.$str_supersubs.'.superfish(params_v);';
	}
	if($conf_jquerysuperfish["menu_navbar"]) {
		$flux .= "\n".'var params_n = {pathClass:"on"};' ;
		if($conf_jquerysuperfish["animation_navbar"]) $flux .= "\n".'params_n.animation = {'.$conf_jquerysuperfish["animation_navbar"].'};' ;
		if($conf_jquerysuperfish["delai_navbar"]) $flux .= "\n".'params_n.delay = '.$conf_jquerysuperfish["delai_navbar"].';' ;
		$flux .= "\n".'$(".'.$conf_jquerysuperfish["classe_navbar"].'").addClass("sf-menu sf-navbar sf-shadow")'.$str_supersubs.'.superfish(params_n);';
	}
	$flux .= "});\n/* ]]> */</script>";
	$flux .= jquerysuperfish_insert_head_css(''); // compat pour les vieux spip
	return $flux;
}

?>