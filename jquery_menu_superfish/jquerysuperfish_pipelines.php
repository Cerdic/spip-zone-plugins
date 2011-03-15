<?php

function jquerysuperfish_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$conf_jquerysuperfish = lire_config('jquerysuperfish');
		$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('css/superfish.css')).'" type="text/css" media="all" />';
		if($conf_jquerysuperfish["menu_vert"]) {
			$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('css/superfish-vertical.css')).'" type="text/css" media="all" />';
		}
	}

	return $flux;
}

function jquerysuperfish_insert_head($flux){
	$conf_jquerysuperfish = lire_config('jquerysuperfish');
	$flux .= "\n".'<script src="'.url_absolue(find_in_path('javascript/hoverIntent.js')).'" type="text/javascript"></script>';
	$flux .= "\n".'<script src="'.url_absolue(find_in_path('javascript/superfish.js')).'" type="text/javascript"></script>';
	$flux .= "\n".'<script type="text/javascript">'."\n".'jQuery(document).ready(function(){' ;
	if($conf_jquerysuperfish["menu_hori"]) {
		$flux .= "\n".'
		$(".'.$conf_jquerysuperfish["classe_hori"].'").addClass("sf-menu sf-shadow").superfish({ 
					animation: {'.$conf_jquerysuperfish["animation_hori"].'},
					delay:'.$conf_jquerysuperfish["delai_hori"].'
			  });';
	}
	if($conf_jquerysuperfish["menu_vert"]) {
		$flux .= "\n".'
		$(".'.$conf_jquerysuperfish["classe_vert"].'").addClass("sf-menu sf-vertical sf-shadow").superfish({ 
					animation: {'.$conf_jquerysuperfish["animation_vert"].'},
					delay:'.$conf_jquerysuperfish["delai_vert"].'
			  });';
	}
	$flux .= "});\n</script>";
	$flux .= jquerysuperfish_insert_head_css(''); // compat pour les vieux spip
	return $flux;
}
//sf-menu sf-vertical sf-js-enabled sf-shadow
?>