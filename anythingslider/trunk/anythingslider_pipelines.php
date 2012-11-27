<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dynamique du js en pied de page,
 * uniquement en presence d'un ou plusieurs sliders sur la page
 * 
 * @param string $flux
 * @return string
 */
function anythingslider_affichage_final($flux){
	if (stripos($flux,'slider-anythingslider')){
		$script = find_in_path('javascript/anythingslider.init.js');
		include_spip('filtres/compresseur');
		if (function_exists('compacte'))
			$script = compacte($script,'js');
		lire_fichier($script, $js);
		$js = "var dir_anythingslider='"._DIR_PLUGIN_ANYTHINGSLIDER."lib/anythingslider/';"
		  . "var css_defaut_anythinslider='".find_in_path("lib/anythingslider/css/anythingslider.css")."';"
		  . $js;
		$js = '<script type="text/javascript">/*<![CDATA[*/'.$js.'/*]]>*/</script>';
		if ($p=stripos($flux,'</body>'))
			$flux = substr_replace($flux,$js,$p,0);
		else
			$flux .= $js;
	}
	return $flux;
}

/**
 * Insertion statique dans l'espace prive, car on ne sait pas faire mieux pour le moment,
 *
 * @param string $flux
 * @return string
 */
function anythingslider_header_prive($flux){
	$js = "var dir_anythingslider='"._DIR_PLUGIN_ANYTHINGSLIDER."lib/anythingslider/';"
	  . "var css_defaut_anythinslider='".find_in_path("lib/anythingslider/css/anythingslider.css")."';";
	$js = '<script type="text/javascript">/*<![CDATA[*/'.$js.'/*]]>*/</script>';

	$flux = $js
		. $flux
		. "<script type='text/javascript' src='".find_in_path('javascript/anythingslider.init.js')."'></script>";
	return $flux;
}

?>
