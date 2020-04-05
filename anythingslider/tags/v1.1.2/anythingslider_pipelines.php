<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function anythingslider_anythingslider_charger($flux){return $flux;}

function anythingslider_insert_head($flux){
	// Chargement de la CSS et du JS d'AnythingSlider
	$css = find_in_path('css/anythingslider.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	$js = find_in_path('js/jquery.anythingslider.min.js');
	$flux .= "<script type='text/javascript' src='$js'></script>\n";
	// Chargement optionnels de scripts et/ou de thèmes
	$config = unserialize($GLOBALS['meta']['anythingslider']);;
	if (!is_array($config))
		$config = array();
	$config = array_unique(pipeline('anythingslider_charger',$config));
	foreach ($config as $script) {
		$ext = substr(strrchr($script, "."), 1);
		if ($fichier = find_in_path($ext.'/'.$script)) {
			if ($ext=='css')
				$flux .= "<link rel='stylesheet' href='$fichier' type='text/css' />\n";
			else
				$flux .= "<script type='text/javascript' src='$fichier'></script>\n";
		}
	}
	// Script pour internet explorer, doit être placé en dernier.
	$css = find_in_path('css/anythingslider-ie.css');
	$flux .= "<!--[if lte IE 7]>\n";
	$flux .= "<link rel='stylesheet' href='$css' type='text/css' />\n";
	$flux .= "<![endif]-->\n";
	return $flux;
}

?>
