<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function anythingslider_anythingslider_charger_fx($flux){return $flux;}

function anythingslider_insert_head($flux){
	$css = find_in_path('css/anythingslider.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	$js = find_in_path('js/jquery.anythingslider.min.js');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	if (pipeline('anythingslider_charger_fx',false)) {
		$js = find_in_path('js/jquery.anythingslider.fx.min.js');
		$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
		$js = find_in_path('js/jquery.easing.1.2.js');
		$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
		$js = find_in_path('js/swfobject.js');
		$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	}
	return $flux;
}


?>
