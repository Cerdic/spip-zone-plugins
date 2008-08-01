<?php

function notation_header_prive($flux){
	$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_NOTATION."css/notation.css' type='text/css' media='all' />\n";
	return $flux;
}

function notation_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_NOTATION.'css/notation.css" type="text/css" media="all" />';
	return $flux;
}

function notation_affichage_final($flux){
    if ((strpos($flux, '<div class="formulaire_notation') == true) or (strpos($flux, "class='formulaire_notation") == true)){
		$incHead .= "\n<link href='"._DIR_PLUGIN_NOTATION."css/jquery.rating.css' type='text/css' rel='stylesheet'/>\n";
		$incHead .= "<script src='"._DIR_PLUGIN_NOTATION."javascript/jquery.MetaData.js' type='text/javascript'></script>\n";
		$incHead .= "<script src='"._DIR_PLUGIN_NOTATION."javascript/jquery.rating.js' type='text/javascript'></script>\n";
		$incHead .= "\n<script type='text/javascript'>\n";
		$incHead .= "function notation_init(){jQuery(function(){\n";
		$incHead .= "jQuery('.formulaire_notation .access').hide();\n";
		$incHead .= "jQuery(function(){ jQuery('input[@type=radio].star').rating(); });\n";
		$incHead .= "jQuery('.auto-submit-star').rating({\n";
		$incHead .= "required: true,\n";
		$incHead .= "callback: function(value, link){\n";
		$incHead .= "jQuery(this.form).submit();\n";
		$incHead .= "}});\n";
		$incHead .= "});}\n";
		$incHead .= "jQuery(function(){notation_init.apply(document); onAjaxLoad(notation_init);});\n";
		$incHead .= "</script>\n";
        return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);
    } else {
		return $flux;
	}
}

?>
