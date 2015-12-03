<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function mathjax_spip_insert_head($flux){
	$flux .= mathjax_spip_flux();
	return $flux;
}

function mathjax_spip_header_prive($flux){
	$flux .= mathjax_spip_flux();
	return $flux;
}
	
function mathjax_spip_flux(){
	$param = "MathJax.Hub.Config({";
	$param .= "tex2jax: {";
	$param .= "	inlineMath: [ ['$','$'] ],";
	$param .= "	processEscapes: true";
	$param .= "}";
	$param .= "});";
	$flux = '<script type="text/x-mathjax-config">'.$param.'</script>';
	if (lire_config('mathjax/mode_dappel', 'cdn') == 'cdn') {
		$flux .= '<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>';
	}
	else if (lire_config('mathjax/mode_dappel', 'cdn') == 'download') {
		$js = find_in_path('lib/mathjax/MathJax.js').'?config=TeX-AMS_HTML';
		$flux .= '<script type="text/javascript" src="'.$js.'"></script>';
	}
	return $flux;
}

?>
